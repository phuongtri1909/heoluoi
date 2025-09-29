<?php

namespace App\Http\Controllers\Admin;

use App\Models\LogoSite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Str;

class LogoSiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $logoSite = LogoSite::first() ?? new LogoSite();
        
        return view('admin.pages.logo-site.edit', compact('logoSite'));
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $logoSite = LogoSite::first() ?? new LogoSite();
        
        return view('admin.pages.logo-site.edit', compact('logoSite'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:1024',
        ]);
        
        $logoSite = LogoSite::first();
        if (!$logoSite) {
            $logoSite = new LogoSite();
        }
        
        if ($request->hasFile('logo')) {
            if ($logoSite->logo) {
                Storage::disk('public')->delete($logoSite->logo);
            }
            
            $logoPaths = $this->processAndSaveLogo($request->file('logo'));
            $logoSite->logo = $logoPaths['original'];
        }
        
        if ($request->hasFile('favicon')) {
            if ($logoSite->favicon) {
                Storage::disk('public')->delete($logoSite->favicon);
            }
            
            $faviconPaths = $this->processAndSaveFavicon($request->file('favicon'));
            $logoSite->favicon = $faviconPaths['original'];
        }
        
        $logoSite->save();
        
        return redirect()->route('admin.logo-site.edit')->with('success', 'Logo và favicon đã được cập nhật thành công');
    }

    /**
     * Delete logo
     */
    public function deleteLogo()
    {
        $logoSite = LogoSite::first();
        if ($logoSite && $logoSite->logo) {
            Storage::disk('public')->delete($logoSite->logo);
            $logoSite->logo = null;
            $logoSite->save();
            
            return redirect()->route('admin.logo-site.edit')->with('success', 'Logo đã được xóa thành công');
        }
        
        return redirect()->route('admin.logo-site.edit')->with('error', 'Không tìm thấy logo để xóa');
    }

    /**
     * Delete favicon
     */
    public function deleteFavicon()
    {
        $logoSite = LogoSite::first();
        if ($logoSite && $logoSite->favicon) {
            Storage::disk('public')->delete($logoSite->favicon);
            $logoSite->favicon = null;
            $logoSite->save();
            
            return redirect()->route('admin.logo-site.edit')->with('success', 'Favicon đã được xóa thành công');
        }
        
        return redirect()->route('admin.logo-site.edit')->with('error', 'Không tìm thấy favicon để xóa');
    }
    
    /**
     * Process and save logo with multiple sizes
     */
    private function processAndSaveLogo($imageFile)
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        Storage::disk('public')->makeDirectory("logos/{$yearMonth}/original");
        Storage::disk('public')->makeDirectory("logos/{$yearMonth}/thumbnail");

        // Original logo (WebP)
        $originalImage = Image::make($imageFile);
        $originalImage->encode('webp', 90);
        Storage::disk('public')->put(
            "logos/{$yearMonth}/original/{$fileName}.webp",
            $originalImage->stream()
        );

        // Thumbnail logo (JPG, height 50px)
        $thumbnailImage = Image::make($imageFile)
            ->resize(null, 50, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('jpg', 70);
        Storage::disk('public')->put(
            "logos/{$yearMonth}/thumbnail/{$fileName}.jpg",
            $thumbnailImage->stream()
        );

        return [
            'original' => "logos/{$yearMonth}/original/{$fileName}.webp",
            'thumbnail' => "logos/{$yearMonth}/thumbnail/{$fileName}.jpg"
        ];
    }
    
    /**
     * Process and save favicon with multiple sizes
     */
    private function processAndSaveFavicon($imageFile)
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = Str::random(8);
        $fileName = "{$timestamp}_{$randomString}";

        Storage::disk('public')->makeDirectory("favicons/{$yearMonth}/original");
        Storage::disk('public')->makeDirectory("favicons/{$yearMonth}/thumbnail");

        // Original favicon (WebP, 32x32)
        $originalImage = Image::make($imageFile)
            ->resize(32, 32)
            ->encode('webp', 90);
        Storage::disk('public')->put(
            "favicons/{$yearMonth}/original/{$fileName}.webp",
            $originalImage->stream()
        );

        // Thumbnail favicon (JPG, 16x16)
        $thumbnailImage = Image::make($imageFile)
            ->resize(16, 16)
            ->encode('jpg', 70);
        Storage::disk('public')->put(
            "favicons/{$yearMonth}/thumbnail/{$fileName}.jpg",
            $thumbnailImage->stream()
        );

        return [
            'original' => "favicons/{$yearMonth}/original/{$fileName}.webp",
            'thumbnail' => "favicons/{$yearMonth}/thumbnail/{$fileName}.jpg"
        ];
    }
}