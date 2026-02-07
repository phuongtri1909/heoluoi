<?php

namespace App\Http\Controllers\Client;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use App\Services\ReadingHistoryService;
use Illuminate\Support\Facades\Storage;

class ZaloAuthController extends Controller
{
    public function __construct(
        protected ReadingHistoryService $readingService
    ) {}

    public function redirect(Request $request)
    {
        $redirectUrl = $request->query('redirect');
        if (!$redirectUrl) {
            $redirectUrl = $request->headers->get('referer');
        }
        if ($redirectUrl && $redirectUrl !== route('login') && $redirectUrl !== route('register') && $redirectUrl !== route('forgot-password')) {
            session(['url.intended' => $redirectUrl]);
        }

        $url = 'https://oauth.zaloapp.com/v4/permission?' . http_build_query([
            'app_id'       => config('services.zalo.app_id'),
            'redirect_uri' => config('services.zalo.redirect'),
            'state'        => csrf_token(),
        ]);

        return redirect($url);
    }

    public function callback(Request $request)
    {
        try {
            if (!$request->has('code')) {
                throw new Exception('Missing authorization code');
            }

            $tokenResponse = Http::asForm()->post(
                'https://oauth.zaloapp.com/v4/access_token',
                [
                    'app_id'     => config('services.zalo.app_id'),
                    'app_secret' => config('services.zalo.secret'),
                    'code'       => $request->code,
                    'grant_type' => 'authorization_code',
                ]
            );

            if (!$tokenResponse->successful()) {
                throw new Exception('Failed to get access token: ' . $tokenResponse->body());
            }

            $token = $tokenResponse->json('access_token');
            if (!$token) {
                throw new Exception('Invalid token response');
            }

            $zaloUserResponse = Http::withHeaders([
                'access_token' => $token,
            ])->get('https://graph.zalo.me/v2.0/me?fields=id,name,picture');

            if (!$zaloUserResponse->successful()) {
                throw new Exception('Failed to get Zalo user info: ' . $zaloUserResponse->body());
            }

            $zaloUser = $zaloUserResponse->json();
            if (empty($zaloUser['id'])) {
                throw new Exception('Invalid Zalo user data');
            }

            $existingUser = User::where('zalo_id', $zaloUser['id'])->first();

            if ($existingUser) {
                $existingUser->active = User::ACTIVE_ACTIVE;
                $existingUser->save();

                Auth::login($existingUser, true);

                $existingUser->remember_token_expires_at = Carbon::now()->addWeeks(2);
                $existingUser->save();

                \App\Models\UserDailyTask::completeTask(
                    $existingUser->id,
                    \App\Models\DailyTask::TYPE_LOGIN,
                    ['login_time' => now()->toISOString()],
                    $request
                );

                $this->readingService->migrateSessionReadingsToUser($existingUser->id);

                if ($existingUser->role === 'admin_main' || $existingUser->role === 'admin_sub') {
                    return redirect()->route('admin.dashboard');
                }

                $intendedUrl = session()->pull('url.intended');
                if ($intendedUrl && $intendedUrl !== route('login') && $intendedUrl !== route('register') && $intendedUrl !== route('forgot-password')) {
                    return redirect($intendedUrl);
                }

                return redirect()->route('home');
            }

            $user = new User();
            $user->name = $zaloUser['name'] ?? 'Zalo User';
            $user->zalo_id = $zaloUser['id'];
            $user->password = bcrypt(Str::random(16));
            $user->active = User::ACTIVE_ACTIVE;

            $avatarUrl = $zaloUser['picture']['data']['url'] ?? null;
            if ($avatarUrl) {
                try {
                    $avatar = file_get_contents($avatarUrl);
                    $tempFile = tempnam(sys_get_temp_dir(), 'zalo_avatar');
                    file_put_contents($tempFile, $avatar);

                    $avatarPaths = $this->processAndSaveAvatar($tempFile);
                    $user->avatar = $avatarPaths['original'];
                    unlink($tempFile);
                } catch (Exception $e) {
                    Log::error('Error processing Zalo avatar:', ['error' => $e->getMessage()]);
                }
            }

            $user->save();

            Auth::login($user, true);

            $user->remember_token_expires_at = Carbon::now()->addWeeks(2);
            $user->save();

            \App\Models\UserDailyTask::completeTask(
                $user->id,
                \App\Models\DailyTask::TYPE_LOGIN,
                ['login_time' => now()->toISOString()],
                $request
            );

            $this->readingService->migrateSessionReadingsToUser($user->id);

            $intendedUrl = session()->pull('url.intended');
            if ($intendedUrl && $intendedUrl !== route('login') && $intendedUrl !== route('register') && $intendedUrl !== route('forgot-password')) {
                return redirect($intendedUrl);
            }

            return redirect()->route('home');
        } catch (Exception $e) {
            Log::error('Zalo login error:', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Đăng nhập Zalo thất bại. Vui lòng thử lại sau.');
        }
    }

    private function processAndSaveAvatar(string $imageFile): array
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        Storage::disk('public')->makeDirectory("avatars/{$yearMonth}/original");
        Storage::disk('public')->makeDirectory("avatars/{$yearMonth}/thumbnail");

        $originalImage = Image::make($imageFile);
        $originalImage->encode('webp', 90);
        Storage::disk('public')->put(
            "avatars/{$yearMonth}/original/{$fileName}.webp",
            $originalImage->stream()
        );

        return [
            'original' => "avatars/{$yearMonth}/original/{$fileName}.webp",
        ];
    }
}
