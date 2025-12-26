<?php

namespace App\Http\Controllers\Client;

use Exception;
use App\Models\User;
use App\Mail\OTPMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\OTPForgotPWMail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use App\Services\ReadingHistoryService;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class AuthController
{

    protected $readingService;

    public function __construct(ReadingHistoryService $readingService)
    {
        $this->readingService = $readingService;
    }

    public function redirectToGoogle()
    {
        // CÁCH 3: Redirect bình thường, JavaScript trong login page sẽ xử lý
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                $existingUser->active = 'active';
                $existingUser->save();
                
                Auth::login($existingUser, true);
                
                $existingUser->remember_token_expires_at = Carbon::now()->addWeeks(2);
                $existingUser->save();
                
                // Complete daily login task
                \App\Models\UserDailyTask::completeTask(
                    $existingUser->id,
                    \App\Models\DailyTask::TYPE_LOGIN,
                    ['login_time' => now()->toISOString()],
                    request()
                );
                
                $readingService = new ReadingHistoryService();
                $readingService->migrateSessionReadingsToUser($existingUser->id);

                if ($existingUser->role == 'admin_main' || $existingUser->role == 'admin_sub') {
                    return redirect()->route('admin.dashboard');
                }

                return redirect()->route('home');
            } else {
                $user = new User();
                $user->name = $googleUser->getName();
                $user->email = $googleUser->getEmail();
                $user->password = bcrypt(Str::random(16)); 
                $user->active = 'active';
                
                if ($googleUser->getAvatar()) {
                    try {
                        $avatar = file_get_contents($googleUser->getAvatar());
                        $tempFile = tempnam(sys_get_temp_dir(), 'avatar');
                        file_put_contents($tempFile, $avatar);

                        $avatarPaths = $this->processAndSaveAvatar($tempFile);
                        $user->avatar = $avatarPaths['original'];
                        unlink($tempFile);
                    } catch (\Exception $e) {
                        Log::error('Error processing Google avatar:', ['error' => $e->getMessage()]);
                    }
                }

                $user->save();
                
                Auth::login($user, true);
                
                $user->remember_token_expires_at = Carbon::now()->addWeeks(2);
                $user->save();

                // Complete daily login task for new Google user
                \App\Models\UserDailyTask::completeTask(
                    $user->id,
                    \App\Models\DailyTask::TYPE_LOGIN,
                    ['login_time' => now()->toISOString()],
                    request()
                );

                return redirect()->route('home');
            }
        } catch (\Exception $e) {
            Log::error('Google login error:', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Đăng nhập bằng Google thất bại. Vui lòng thử lại sau.');
        }
    }

    private function processAndSaveAvatar($imageFile)
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        // Create directories if they don't exist
        Storage::disk('public')->makeDirectory("avatars/{$yearMonth}/original");
        Storage::disk('public')->makeDirectory("avatars/{$yearMonth}/thumbnail");

        // Process original image
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

    public function register(Request $request)
    {

        if ($request->has('email') && $request->has('otp') && $request->has('password')) {
            try {
                $request->validate([
                    'email' => 'required|email',
                    'otp' => 'required',
                    'password' => 'required|min:6',
                    'name' => 'required|max:255',
                    'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp', // Remove required
                ], [
                    'email.required' => 'Hãy nhập email của bạn vào đi',
                    'email.email' => 'Email bạn nhập không hợp lệ rồi',
                    'otp.required' => 'Hãy nhập mã OTP của bạn vào đi',
                    'password.required' => 'Hãy nhập mật khẩu của bạn vào đi',
                    'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                    'name.required' => 'Hãy nhập tên của bạn vào đi',
                    'name.max' => 'Tên của bạn quá dài rồi',
                    'avatar.required' => 'Hãy chọn ảnh đại diện của bạn',
                    'avatar.image' => 'Ảnh bạn chọn không hợp lệ',
                    'avatar.mimes' => 'Ảnh bạn chọn phải có định dạng jpeg, png, jpg, gif, svg, webp',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->errors()
                ], 422);
            }

            try {
                $user = User::where('email', $request->email)->first();
                if (!$user) {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['email' => ['Email này không hợp lệ']],
                    ], 422);
                }

                if (!password_verify($request->otp, $user->key_active)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['otp' => ['Mã OTP không chính xác']],
                    ], 422);
                }
                $user->key_active = null;
                $user->name = $request->name;
                $user->password = bcrypt($request->password);
                $user->active = 'active';

                // Handle avatar upload if provided
                if ($request->hasFile('avatar')) {
                    try {
                        $avatarPaths = $this->processAndSaveAvatar($request->file('avatar'));
                        $user->avatar = $avatarPaths['original'];
                    } catch (\Exception $e) {
                        Log::error('Error processing avatar:', ['error' => $e->getMessage()]);
                        // Continue without avatar if there's an error
                    }
                }

                $user->save();

                Auth::login($user);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Đăng ký thành công, chào mừng bạn đến với ' . env('APP_NAME'),
                    'url' => route('home'),
                ]);
            } catch (Exception $e) {
                Log::error('Registration error:', ['error' => $e->getMessage()]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Đã xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại sau.',
                    'error' => 'Đã xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại sau.',
                ], 500);
            }
        }
        try {
            $request->validate([
                'email' => 'required|email',
            ], [
                'email.required' => 'Hãy nhập email của bạn vào đi',
                'email.email' => 'Email bạn nhập không hợp lệ rồi',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Trả về lỗi validate dưới dạng JSON
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                if ($user->active == 'active') {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['email' => ['Email này đã tồn tại, hãy dùng email khác']],
                    ], 422);
                }

                if (!$user->updated_at->lt(Carbon::now()->subMinutes(3)) && $user->key_active != null) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Dùng lại OTP đã gửi trước đó, nhận OTP mới sau 3 phút',
                    ], 200);
                }
            } else {
                $user = new User();
                $user->email = $request->email;
            }

            $randomPassword = Str::random(10);
            $user->password = bcrypt($randomPassword);

            $otp = generateRandomOTP();
            $user->save();

            Mail::to($user->email)->send(new OTPMail($otp));
            $user->key_active = bcrypt($otp);
            $user->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Đăng ký thành công, hãy kiểm tra email của bạn để lấy mã OTP',
            ]);
        } catch (Exception $e) {
            Log::error('Registration error:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại sau.',
                'error' => 'Đã xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại sau.',
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Hãy nhập email của bạn vào đi',
            'email.email' => 'Email bạn nhập không hợp lệ rồi',
            'password.required' => 'Hãy nhập mật khẩu của bạn vào đi',
        ]);

        try {

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Thông tin xác thực không chính xác',
                ]);
            }

            if ($user->active == 'inactive') {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Thông tin xác thực không chính xác',
                ]);
            }

            if (!password_verify($request->password, $user->password)) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Thông tin xác thực không chính xác',
                ]);
            }

            $remember = $request->has('remember') && $request->remember == '1';
            
            Auth::login($user, $remember);
            
            if ($remember) {
                $user->remember_token_expires_at = Carbon::now()->addWeeks(2);
                $user->save();
            } else {
                $user->remember_token_expires_at = null;
                $user->save();
            }

            $user->ip_address = $request->ip();
            $user->save();
            
            // Complete daily login task
            \App\Models\UserDailyTask::completeTask(
                $user->id,
                \App\Models\DailyTask::TYPE_LOGIN,
                ['login_time' => now()->toISOString()],
                $request
            );
            
            // Chuyển dữ liệu đọc từ session sang user
            $readingService = new ReadingHistoryService();
            $readingService->migrateSessionReadingsToUser($user->id);

            if ($user->role == 'admin_main' || $user->role == 'admin_sub') {
                return redirect()->route('admin.dashboard');
            }


            return redirect()->route('home');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Đã xảy ra lỗi trong quá trình đăng nhập. Vui lòng thử lại sau.');
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $this->readingService->copyUserReadingsToSession(Auth::id());
        }
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route(('home'));
    }

    public function forgotPassword(Request $request)
    {
        if ($request->has('email')) {
            try {
                $request->validate([
                    'email' => 'required|email',
                ], [
                    'email.required' => 'Hãy nhập email của bạn vào đi',
                    'email.email' => 'Email bạn nhập không hợp lệ rồi',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->errors()
                ], 422);
            }

            try {
                $user = User::where('email', $request->email)->first();
                if (!$user || $user->active == 'inactive') {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['email' => ['Thông tin xác thực không chính xác']],
                    ], 422);
                }



                if ($request->has('email') && $request->has('otp')) {

                    try {
                        $request->validate([
                            'otp' => 'required',
                        ], [
                            'otp.required' => 'Hãy nhập mã OTP của bạn vào đi',
                        ]);
                    } catch (\Illuminate\Validation\ValidationException $e) {
                        return response()->json([
                            'status' => 'error',
                            'message' => $e->errors()
                        ], 422);
                    }

                    if (!password_verify($request->otp, $user->key_reset_password)) {
                        return response()->json([
                            'status' => 'error',
                            'message' => ['otp' => ['Mã OTP không chính xác']],
                        ], 422);
                    }

                    if ($request->has('email') && $request->has('otp') && $request->has('password')) {
                        try {
                            $request->validate([
                                'password' => 'required|min:6',
                            ], [
                                'password.required' => 'Hãy nhập mật khẩu của bạn vào đi',
                                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                            ]);
                        } catch (\Illuminate\Validation\ValidationException $e) {
                            return response()->json([
                                'status' => 'error',
                                'message' => $e->errors()
                            ], 422);
                        }

                        try {

                            $user->key_reset_password = null;
                            $user->password = bcrypt($request->password);
                            $user->save();

                            Auth::login($user);

                            return response()->json([
                                'status' => 'success',
                                'message' => 'Đặt lại mật khẩu thành công',
                                'url' => route('home'),
                            ]);
                        } catch (Exception $e) {
                            Log::error('Reset password error:', ['error' => $e->getMessage()]);
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Đã xảy ra lỗi trong quá trình đặt lại mật khẩu. Vui lòng thử lại sau.',
                                'error' => 'Đã xảy ra lỗi trong quá trình đặt lại mật khẩu. Vui lòng thử lại sau.',
                            ], 500);
                        }
                    }

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Hãy nhập mật khẩu mới của bạn',
                    ], 200);
                }

                if ($user->reset_password_at != null) {
                    $resetPasswordAt = Carbon::parse($user->reset_password_at);
                    if (!$resetPasswordAt->lt(Carbon::now()->subMinutes(3))) {
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Dùng lại OTP đã gửi trước đó, nhận OTP mới sau 3 phút',
                        ], 200);
                    }
                }

                $randomOTPForgotPW = generateRandomOTP();
                $user->key_reset_password = bcrypt($randomOTPForgotPW);
                $user->reset_password_at = Carbon::now();
                $user->save();

                Mail::to($user->email)->send(new OTPForgotPWMail($randomOTPForgotPW));
                return response()->json([
                    'status' => 'success',
                    'message' => 'Hãy kiểm tra email của bạn để lấy mã OTP',
                ], 200);
            } catch (Exception $e) {
                Log::error('Forgot password error:', ['error' => $e->getMessage()]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Đã xảy ra lỗi trong quá trình đặt lại mật khẩu. Vui lòng thử lại sau.',
                    'error' => 'Đã xảy ra lỗi trong quá trình đặt lại mật khẩu. Vui lòng thử lại sau.',
                ], 500);
            }
        }
    }

    public function changePassword() {}
}
