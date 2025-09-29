<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Banner;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\Controller;

class BannerController extends Controller
{
    private function processAndSaveImage($imageFile)
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        // Tạo thư mục
        Storage::disk('public')->makeDirectory("banners/{$yearMonth}/original");
        Storage::disk('public')->makeDirectory("banners/{$yearMonth}/desktop");
        Storage::disk('public')->makeDirectory("banners/{$yearMonth}/mobile");

        // Lưu ảnh gốc (WebP)
        $originalImage = Image::make($imageFile);
        $originalImage->encode('webp', 90);
        Storage::disk('public')->put(
            "banners/{$yearMonth}/original/{$fileName}.webp",
            $originalImage->stream()
        );

        // Lưu ảnh desktop (1920px width)
        $desktopImage = Image::make($imageFile)
            ->resize(1920, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('webp', 80);
        Storage::disk('public')->put(
            "banners/{$yearMonth}/desktop/{$fileName}.webp",
            $desktopImage->stream()
        );

        // Lưu ảnh mobile (767px width)
        $mobileImage = Image::make($imageFile)
            ->resize(767, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('webp', 70);
        Storage::disk('public')->put(
            "banners/{$yearMonth}/mobile/{$fileName}.webp",
            $mobileImage->stream()
        );

        return [
            'original' => "banners/{$yearMonth}/original/{$fileName}.webp",
            'desktop' => "banners/{$yearMonth}/desktop/{$fileName}.webp",
            'mobile' => "banners/{$yearMonth}/mobile/{$fileName}.webp"
        ];
    }

    public function index()
    {
        $banners = Banner::with('story')->paginate(10);
        return view('admin.pages.banners.index', compact('banners'));
    }

    public function create()
    {
        $stories = Story::orderBy('title')->get();
        return view('admin.pages.banners.create', compact('stories'));
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateBanner($request);

        if ($request->hasFile('image')) {
            $imagePaths = $this->processAndSaveImage($request->file('image'));
            $validatedData['image'] = $imagePaths['original'];
        }

        // Validation: Chỉ được chọn một trong hai - story_id hoặc link
        if (!empty($validatedData['story_id']) && !empty($validatedData['link'])) {
            return back()->withInput()->withErrors(['story_id' => 'Chỉ được chọn truyện hoặc nhập URL, không được chọn cả hai']);
        }
        
        if (empty($validatedData['story_id']) && empty($validatedData['link'])) {
            return back()->withInput()->withErrors(['link' => 'Vui lòng chọn truyện hoặc nhập URL liên kết']);
        }

        Banner::create($validatedData);

        return redirect()->route('admin.banners.index')->with('success', 'Banner đã được tạo thành công');
    }

    public function show(Banner $banner)
    {
        return view('admin.pages.banners.show', compact('banner'));
    }

    public function edit(Banner $banner)
    {
        $stories = Story::orderBy('title')->get();
        return view('admin.pages.banners.edit', compact('banner', 'stories'));
    }

    public function update(Request $request, Banner $banner)
    {
        $validatedData = $this->validateBanner($request, $banner->id);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            if ($banner->image) {
                $this->deleteBannerImages($banner->image);
            }

            $imagePaths = $this->processAndSaveImage($request->file('image'));
            $validatedData['image'] = $imagePaths['original'];
        }

        // Validation: Chỉ được chọn một trong hai - story_id hoặc link
        if (!empty($validatedData['story_id']) && !empty($validatedData['link'])) {
            return back()->withInput()->withErrors(['story_id' => 'Chỉ được chọn truyện hoặc nhập URL, không được chọn cả hai']);
        }
        
        if (empty($validatedData['story_id']) && empty($validatedData['link'])) {
            return back()->withInput()->withErrors(['link' => 'Vui lòng chọn truyện hoặc nhập URL liên kết']);
        }

        $banner->update($validatedData);

        return redirect()->route('admin.banners.index')->with('success', 'Banner đã được cập nhật thành công');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            $this->deleteBannerImages($banner->image);
        }

        $banner->delete();

        return redirect()->back()->with('success', 'Banner đã được xóa thành công');
    }

    private function validateBanner(Request $request, $id = null)
    {
        $rules = [
            'image' => $id ? 'nullable|image|mimes:jpeg,png,jpg,gif' : 'required|image|mimes:jpeg,png,jpg,gif',
            'link' => 'nullable|url|max:255',
            'story_id' => 'nullable|exists:stories,id',
            'status' => 'required|boolean',
        ];

        $messages = [
            'image.required' => 'Hình ảnh là bắt buộc',
            'image.image' => 'Tập tin phải là hình ảnh',
            'image.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg hoặc gif',
            'link.url' => 'Link không hợp lệ',
            'link.max' => 'Link không được vượt quá 255 ký tự',
            'story_id.exists' => 'Truyện không tồn tại',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.boolean' => 'Trạng thái không hợp lệ',
        ];

        return $request->validate($rules, $messages);
    }

    private function deleteBannerImages($imagePath)
    {
        if (!$imagePath) {
            return;
        }

        // Lấy thông tin từ đường dẫn ảnh gốc
        $pathInfo = pathinfo($imagePath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];

        // Xóa 3 phiên bản ảnh
        $imagesToDelete = [
            $imagePath, // original
            $directory . '/desktop/' . $filename . '.webp',
            $directory . '/mobile/' . $filename . '.webp'
        ];

        Storage::disk('public')->delete($imagesToDelete);
    }

}
