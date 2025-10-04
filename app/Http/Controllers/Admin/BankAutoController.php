<?php

namespace App\Http\Controllers\Admin;

use App\Models\BankAuto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class BankAutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bankAutos = BankAuto::latest()->paginate(10);
        return view('admin.pages.bank-autos.index', compact('bankAutos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.bank-autos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'boolean'
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status');

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('bank-autos/logos', 'public');
        }

        // Handle QR code upload
        if ($request->hasFile('qr_code')) {
            $data['qr_code'] = $request->file('qr_code')->store('bank-autos/qr-codes', 'public');
        }

        BankAuto::create($data);

        return redirect()->route('admin.bank-autos.index')
            ->with('success', 'Ngân hàng tự động đã được thêm thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BankAuto $bankAuto)
    {
        return view('admin.pages.bank-autos.show', compact('bankAuto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAuto $bankAuto)
    {
        return view('admin.pages.bank-autos.edit', compact('bankAuto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankAuto $bankAuto)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'boolean'
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status');

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($bankAuto->logo) {
                Storage::disk('public')->delete($bankAuto->logo);
            }
            $data['logo'] = $request->file('logo')->store('bank-autos/logos', 'public');
        }

        // Handle QR code upload
        if ($request->hasFile('qr_code')) {
            // Delete old QR code
            if ($bankAuto->qr_code) {
                Storage::disk('public')->delete($bankAuto->qr_code);
            }
            $data['qr_code'] = $request->file('qr_code')->store('bank-autos/qr-codes', 'public');
        }

        $bankAuto->update($data);

        return redirect()->route('admin.bank-autos.index')
            ->with('success', 'Ngân hàng tự động đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAuto $bankAuto)
    {
        // Delete logo and QR code files
        if ($bankAuto->logo) {
            Storage::disk('public')->delete($bankAuto->logo);
        }
        if ($bankAuto->qr_code) {
            Storage::disk('public')->delete($bankAuto->qr_code);
        }

        $bankAuto->delete();

        return redirect()->route('admin.bank-autos.index')
            ->with('success', 'Ngân hàng tự động đã được xóa thành công!');
    }
}