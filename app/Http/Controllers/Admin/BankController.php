<?php

namespace App\Http\Controllers\Admin;

use App\Models\Bank;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banks = Bank::paginate(10);
        return view('admin.pages.banks.index', compact('banks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.banks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'code', 'account_number', 'account_name']);

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->processAndSaveImage($request->file('logo'), 'banks/logos');
        }

        if ($request->hasFile('qr_code')) {
            $data['qr_code'] = $this->processAndSaveImage($request->file('qr_code'), 'banks/qr_codes');
        }

        $data['status'] = $request->has('status');

        Bank::create($data);

        return redirect()->route('admin.banks.index')
            ->with('success', 'Ngân hàng đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bank $bank)
    {
        return view('admin.pages.banks.show', compact('bank'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bank $bank)
    {
        return view('admin.pages.banks.edit', compact('bank'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'code', 'account_number', 'account_name']);

        if ($request->hasFile('logo')) {
            if ($bank->logo) {
                Storage::disk('public')->delete($bank->logo);
            }
            $data['logo'] = $this->processAndSaveImage($request->file('logo'), 'banks/logos');
        }

        if ($request->hasFile('qr_code')) {
            if ($bank->qr_code) {
                Storage::disk('public')->delete($bank->qr_code);
            }
            $data['qr_code'] = $this->processAndSaveImage($request->file('qr_code'), 'banks/qr_codes');
        }
        $data['status'] = $request->has('status');

        $bank->update($data);

        return redirect()->route('admin.banks.index')
            ->with('success', 'Thông tin ngân hàng đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bank $bank)
    {
        if ($bank->deposits()->exists()) {
            return redirect()->route('admin.banks.index')
                ->with('error', 'Không thể xóa ngân hàng này vì đã có giao dịch liên quan.');
        }

        if ($bank->logo) {
            Storage::disk('public')->delete($bank->logo);
        }

        if ($bank->qr_code) {
            Storage::disk('public')->delete($bank->qr_code);
        }

        $bank->delete();

        return redirect()->route('admin.banks.index')
            ->with('success', 'Ngân hàng đã được xóa thành công.');
    }

    /**
     * Process and save image file
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @return string
     */
    private function processAndSaveImage($file, $path)
    {
        $now = Carbon::now();
        $yearMonth = $now->format('Y/m');
        $timestamp = $now->format('YmdHis');
        $randomString = substr(md5(uniqid()), 0, 8);
        $fileName = "{$timestamp}_{$randomString}";

        Storage::disk('public')->makeDirectory("{$path}/{$yearMonth}");

        $image = Image::make($file);
        
        $image->resize(800, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        
        $image->encode('webp', 85);

        Storage::disk('public')->put(
            "{$path}/{$yearMonth}/{$fileName}.webp",
            $image->stream()
        );

        return "{$path}/{$yearMonth}/{$fileName}.webp";
    }
}
