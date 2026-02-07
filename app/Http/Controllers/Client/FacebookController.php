<?php

namespace App\Http\Controllers\Client;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\FacebookSetting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
            $existingUser = User::where('facebook_id', $facebookUser->id)->first();

            if ($existingUser) {
                $existingUser->active = User::ACTIVE_ACTIVE;
                $existingUser->save();
                Auth::login($existingUser);

                return redirect()->route('home');
            } else {
                $user = new User();
                $user->name = $facebookUser->name;
                $user->facebook_id  = $facebookUser->id;
                $user->password = bcrypt(Str::random(16)); 
                $user->active = User::ACTIVE_ACTIVE;
                
                if ($facebookUser->avatar) {
                    try {
                        $avatar = file_get_contents($facebookUser->avatar);
                        $tempFile = tempnam(sys_get_temp_dir(), 'avatar');
                        file_put_contents($tempFile, $avatar);

                        $avatarPaths = $this->processAndSaveAvatar($tempFile);
                        $user->avatar = $avatarPaths['original'];
                        unlink($tempFile);
                    } catch (\Exception $e) {
                        Log::error('Error processing Facebook avatar:', ['error' => $e->getMessage()]);
                    }
                }

                $user->save();
                Auth::login($user);

                return redirect()->route('home');
            }
        } catch (Exception $e) {
            Log::error('Facebook login error:', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Đăng nhập Facebook thất bại!');
        }
    }

    private function processAndSaveAvatar($imageFile)
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        Storage::disk('public')->makeDirectory("avatars/{$yearMonth}/original");

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