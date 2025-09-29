<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(10);
        return view('admin.pages.category.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.pages.category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories|max:255',
            'description' => 'nullable',
        ], [
            'name.required' => 'Tên thể loại không được để trống.',
            'name.unique' => 'Tên thể loại đã tồn tại.',
            'name.max' => 'Tên thể loại không được vượt quá 255 ký tự.'
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_main' => $request->has('is_main')
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Thể loại đã được tạo thành công.');
    }

    public function edit(Category $category)
    {
        return view('admin.pages.category.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
      
        $request->validate([
            'name' => 'required|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable',
        ], [
            'name.required' => 'Tên thể loại không được để trống.',
            'name.unique' => 'Tên thể loại đã tồn tại.',
            'name.max' => 'Tên thể loại không được vượt quá 255 ký tự.',
        ]);

        try {
            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'is_main' => $request->has('is_main')
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating category:', ['error' => $e->getMessage()]);
            return redirect()->route('categories.edit', $category)
                ->with('error', 'Có lỗi xảy ra khi cập nhật thể loại.')->withInput();
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Thể loại đã được cập nhật thành công.');
    }

    public function show(Category $category)
    {
        $stories = $category->stories()
            ->with('user')
            ->withCount('chapters')
            ->latest()
            ->paginate(15);

        return view('admin.pages.category.show', compact('category', 'stories'));
    }

    public function destroy(Category $category)
    {
        try {
            $category->delete();
        } catch (\Exception $e) {
            Log::error('Error deleting category:', ['error' => $e->getMessage()]);
            return redirect()->route('admin.categories.index')
                ->with('error', 'Có lỗi xảy ra khi xóa thể loại.');
        }
        return redirect()->route('admin.categories.index')
            ->with('success', 'Thể loại đã được xóa thành công.');
    }
}
