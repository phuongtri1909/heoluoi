<?php

namespace App\Http\Controllers\Admin;

use App\Models\Social;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class SocialController extends Controller
{
    /**
     * Display a listing of social media links.
     */
    public function index()
    {
        $socials = Social::orderBy('sort_order')->get();
        
        $fontAwesomeIcons = [
            'fab fa-facebook-f' => 'Facebook',
            'fab fa-twitter' => 'Twitter',
            'fab fa-instagram' => 'Instagram',
            'fab fa-youtube' => 'YouTube',
            'fab fa-tiktok' => 'TikTok',
            'fab fa-pinterest' => 'Pinterest',
            'fab fa-discord' => 'Discord',
            'fab fa-telegram' => 'Telegram',
            'fab fa-linkedin' => 'LinkedIn',
            'fab fa-github' => 'GitHub',
            'fab fa-reddit' => 'Reddit',
            'fab fa-snapchat' => 'Snapchat',
            'fab fa-whatsapp' => 'WhatsApp',
            'fab fa-line' => 'Line',
            
            'fas fa-envelope' => 'Email',
            'fas fa-globe' => 'Website',
            'fas fa-phone' => 'Phone',
            'fas fa-map-marker-alt' => 'Location',
            'fas fa-rss' => 'RSS Feed',
            
            'custom-zalo' => 'Zalo',
        ];
        
        return view('admin.pages.socials.index', compact('socials', 'fontAwesomeIcons'));
    }

    /**
     * Store a newly created social media link.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'icon' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Tên mạng xã hội không được để trống',
            'url.required' => 'URL không được để trống',
            'url.url' => 'URL không hợp lệ',
            'icon.required' => 'Icon không được để trống',
        ]);
        
        Social::create([
            'name' => $request->name,
            'url' => $request->url,
            'icon' => $request->icon,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);
        
        Cache::forget('socials');
        
        return redirect()->route('admin.socials.index')
            ->with('success', 'Thêm mạng xã hội thành công');
    }

    /**
     * Update the specified social media link.
     */
    public function update(Request $request, Social $social)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'icon' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Tên mạng xã hội không được để trống',
            'url.required' => 'URL không được để trống',
            'url.url' => 'URL không hợp lệ',
            'icon.required' => 'Icon không được để trống',
        ]);
        
        $social->update([
            'name' => $request->name,
            'url' => $request->url,
            'icon' => $request->icon,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);
        
        Cache::forget('socials');
        
        return redirect()->route('admin.socials.index')
            ->with('success', 'Cập nhật mạng xã hội thành công');
    }

    /**
     * Remove the specified social media link.
     */
    public function destroy(Social $social)
    {
        $social->delete();
        
        Cache::forget('socials');
        
        return redirect()->route('admin.socials.index')
            ->with('success', 'Xóa mạng xã hội thành công');
    }
} 