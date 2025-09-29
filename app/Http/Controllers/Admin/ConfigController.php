<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    /**
     * Display a listing of the configurations.
     */
    public function index()
    {
        $configs = Config::orderBy('key')->get();
        return view('admin.pages.configs.index', compact('configs'));
    }

    /**
     * Show the form for creating a new configuration.
     */
    public function create()
    {
        return view('admin.pages.configs.create');
    }

    /**
     * Store a newly created configuration in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:configs',
            'value' => 'required|string',
            'description' => 'nullable|string',
        ]);

        Config::setConfig($request->key, $request->value, $request->description);

        return redirect()->route('admin.configs.index')
            ->with('success', 'Cấu hình đã được tạo thành công.');
    }

    /**
     * Show the form for editing the specified configuration.
     */
    public function edit(Config $config)
    {
        return view('admin.pages.configs.edit', compact('config'));
    }

    /**
     * Update the specified configuration in storage.
     */
    public function update(Request $request, Config $config)
    {
        $request->validate([
            'value' => 'required|string',
            'description' => 'nullable|string',
        ]);

        Config::setConfig($config->key, $request->value, $request->description);

        return redirect()->route('admin.configs.index')
            ->with('success', 'Cấu hình đã được cập nhật thành công.');
    }

    /**
     * Remove the specified configuration from storage.
     */
    public function destroy(Config $config)
    {
        $config->delete();

        return redirect()->route('admin.configs.index')
            ->with('success', 'Cấu hình đã được xóa thành công.');
    }
} 