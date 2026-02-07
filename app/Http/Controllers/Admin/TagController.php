<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::orderBy('name')->paginate(15);
        return view('admin.pages.tag.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.pages.tag.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:tags,name|max:255',
        ], [
            'name.required' => 'Tên chủ đề không được để trống.',
            'name.unique' => 'Tên chủ đề đã tồn tại.',
            'name.max' => 'Tên chủ đề không được vượt quá 255 ký tự.',
        ]);

        Tag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.tags.index')
            ->with('success', 'Chủ đề đã được tạo thành công.');
    }

    public function edit(Tag $tag)
    {
        return view('admin.pages.tag.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|max:255|unique:tags,name,' . $tag->id,
        ], [
            'name.required' => 'Tên chủ đề không được để trống.',
            'name.unique' => 'Tên chủ đề đã tồn tại.',
            'name.max' => 'Tên chủ đề không được vượt quá 255 ký tự.',
        ]);

        $tag->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.tags.index')
            ->with('success', 'Chủ đề đã được cập nhật thành công.');
    }

    public function destroy(Tag $tag)
    {
        $tag->stories()->update(['tag_id' => null]);
        $tag->delete();
        return redirect()->route('admin.tags.index')
            ->with('success', 'Chủ đề đã được xóa thành công.');
    }
}
