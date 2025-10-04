<?php

namespace App\Http\Controllers\Client;

use App\Models\Bank;
use App\Models\User;
use App\Models\Bookmark;
use App\Models\BanIp;
use App\Models\UserReading;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Mail\OTPUpdateUserMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Intervention\Image\Facades\Image;
use App\Services\ReadingHistoryService;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if ($request->has('otp')) {
            $otp = $request->otp;

            if (!password_verify($otp, $user->key_reset_password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => ['otp' => ['Mã OTP không chính xác để thay đổi mật khẩu']],
                ], 422);
            }

            if ($request->has('password') && $request->has('password_confirmation')) {
                try {
                    $request->validate([
                        'password' => 'required|min:6|confirmed',
                    ], [
                        'password.required' => 'Hãy nhập mật khẩu mới',
                        'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                        'password.confirmed' => 'Mật khẩu xác nhận không khớp',
                    ]);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $e->errors()
                    ], 422);
                }

                $user->password = bcrypt($request->password);
                $user->key_reset_password = null;

                $user->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Cập nhật mật khẩu thành công',
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Xác thực OTP thành công',
            ], 200);
        }

        $otp = generateRandomOTP();
        if ($user->reset_password_at != null) {
            $resetPasswordAt = Carbon::parse($user->reset_password_at);
            if (!$resetPasswordAt->lt(Carbon::now()->subMinutes(3))) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'OTP đã được gửi đến Email của bạn, hoặc thử lại sau 3 phút',
                ], 200);
            }
        }

        $user->key_reset_password = bcrypt($otp);
        $user->reset_password_at = now();
        $user->save();

        Mail::to($user->email)->send(new OTPUpdateUserMail($otp, 'password'));
        return response()->json([
            'status' => 'success',
            'message' => 'Gửi mã OTP thành công, vui lòng kiểm tra Email của bạn',
        ], 200);
    }

    public function userProfile()
    {
        $user = Auth::user();

        return view('pages.information.profile', compact('user'));
    }

    public function bookmarks()
    {
        $user = Auth::user();
        $bookmarks = Bookmark::where('user_id', $user->id)
            ->with(['story' => function ($query) {
                $query->with('latestChapter');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.information.bookmarks', compact('bookmarks'));
    }
    
    // Remove bookmark
    public function removeBookmark(Request $request)
    {
        $request->validate([
            'bookmark_id' => 'required|exists:bookmarks,id',
        ]);

        $bookmark = Bookmark::findOrFail($request->bookmark_id);

        // Check if the bookmark belongs to the current user
        if ($bookmark->user_id != Auth::id()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền thực hiện hành động này'
                ], 403);
            }
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện hành động này');
        }

        $bookmark->delete();

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Đã xóa truyện khỏi danh sách theo dõi'
            ]);
        }

        return redirect()->back()->with('success', 'Đã xóa truyện khỏi danh sách theo dõi');
    }

    // Toggle bookmark notification
    public function toggleBookmarkNotification(Request $request)
    {
        $request->validate([
            'bookmark_id' => 'required|exists:bookmarks,id',
        ]);

        $bookmark = Bookmark::findOrFail($request->bookmark_id);

        // Check if the bookmark belongs to the current user
        if ($bookmark->user_id != Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn không có quyền thực hiện hành động này'
            ], 403);
        }

        $result = Bookmark::toggleNotification($request->bookmark_id);

        return response()->json($result);
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

    public function updateAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            ], [
                'avatar.required' => 'Hãy chọn ảnh avatar',
                'avatar.image' => 'Avatar phải là ảnh',
                'avatar.mimes' => 'Chỉ chấp nhận ảnh định dạng jpeg, png, jpg hoặc gif',
                'avatar.max' => 'Dung lượng avatar không được vượt quá 4MB'
            ]);

            $user = Auth::user();
            DB::beginTransaction();

            try {
                // Store old avatar paths for deletion
                $oldAvatar = $user->avatar;
                $oldAvatarThumbnail = $user->avatar_thumbnail;

                // Process and save new avatar
                $avatarPaths = $this->processAndSaveAvatar($request->file('avatar'));

                // Update user avatar path
                $user->avatar = $avatarPaths['original'];
                $user->save();

                DB::commit();

                // Delete old avatars after successful update
                if ($oldAvatar) {
                    Storage::disk('public')->delete($oldAvatar);
                }
                if ($oldAvatarThumbnail) {
                    Storage::disk('public')->delete($oldAvatarThumbnail);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Cập nhật avatar thành công',
                    'avatar' => $avatarPaths['original'],
                    'avatar_url' => Storage::url($avatarPaths['original']),
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();

                // Delete new avatar if it was uploaded
                if (isset($avatarPaths)) {
                    Storage::disk('public')->delete([
                        $avatarPaths['original'],
                    ]);
                }

                \Log::error('Avatar update error:', ['error' => $e->getMessage()]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Có lỗi xảy ra, vui lòng thử lại sau'
                ], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        }
    }

    public function updateNameOrPhone(Request $request)
    {

        if ($request->has('name')) {
            try {
                $request->validate([
                    'name' => 'required|string|min:3|max:255',
                ], [
                    'name.required' => 'Hãy nhập tên',
                    'name.string' => 'Tên phải là chuỗi',
                    'name.min' => 'Tên phải có ít nhất 3 ký tự',
                    'name.max' => 'Tên không được vượt quá 255 ký tự'
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return redirect()->route('user.profile')->with('error', $e->errors());
            }

            try {
                $user = Auth::user();
                $user->name = $request->name;
                $user->save();
                return redirect()->route('user.profile')->with('success', 'Cập nhật tên thành công');
            } catch (\Exception $e) {
                return redirect()->route('user.profile')->with('error', 'Cập nhật tên thất bại');
            }
        } elseif ($request->has('phone')) {

            try {
                $request->validate([
                    'phone' => 'required|string|min:10|max:10',
                ], [
                    'phone.required' => 'Hãy nhập số điện thoại',
                    'phone.string' => 'Số điện thoại phải là chuỗi',
                    'phone.min' => 'Số điện thoại phải có ít nhất 10 ký tự',
                    'phone.max' => 'Số điện thoại không được vượt quá 10 ký tự'
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return redirect()->route('user.profile')->with('error', $e->errors());
            }

            try {
                $user = Auth::user();
                $user->phone = $request->phone;
                $user->save();
                return redirect()->route('user.profile')->with('success', 'Cập nhật số điện thoại thành công');
            } catch (\Exception $e) {
                return redirect()->route('user.profile')->with('error', 'Cập nhật số điện thoại thất bại');
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu không hợp lệ'
            ], 422);
        }
    }

    public function readingHistory()
    {
        // Get user reading history from database
        $readingHistory = UserReading::with([
            'story' => function ($query) {
                $query->withCount(['chapters' => function ($q) {
                    $q->where('status', 'published');
                }]);
            },
            'chapter'
        ])
            ->where('user_id', Auth::id())
            ->orderByDesc('updated_at')
            ->get();

        return view('pages.information.reading_history', compact('readingHistory'));
    }

    public function userPurchases()
    {
        // Get user's purchased chapters
        $purchasedChapters = Auth::user()->chapterPurchases()
            ->with(['chapter.story'])
            ->orderByDesc('created_at')
            ->get();

        // Get user's purchased story combos
        $purchasedStories = Auth::user()->storyPurchases()
            ->with(['story'])
            ->orderByDesc('created_at')
            ->get();

        return view('pages.information.purchases', compact('purchasedChapters', 'purchasedStories'));
    }

    public function clearReadingHistory()
    {
        // Delete all reading history for the current user
        UserReading::where('user_id', Auth::id())->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Lịch sử đọc truyện đã được xóa'
        ]);
    }
}
