<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'store_name' => Setting::get('store_name', 'POS App'),
            'store_address' => Setting::get('store_address', 'Jl. Contoh Alamat No. 123'),
            'store_phone' => Setting::get('store_phone', '0812-3456-7890'),
            'receipt_footer' => Setting::get('receipt_footer', 'Terima kasih telah berbelanja!'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:100',
            'store_address' => 'required|string|max:255',
            'store_phone' => 'required|string|max:20',
            'receipt_footer' => 'nullable|string|max:150',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
            // Hapus cache tiap key biar langsung berubah di tampilan
            Cache::forget('setting_' . $key); 
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil diperbarui!');
    }
}