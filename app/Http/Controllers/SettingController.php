<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'store_name' => Setting::get('store_name', 'POS App'),
            'store_address' => Setting::get('store_address', 'Jl. Contoh Alamat No. 123'),
            'store_phone' => Setting::get('store_phone', '0812-3456-7890'),
            'receipt_footer' => Setting::get('receipt_footer', 'Terima kasih telah berbelanja!'),
            'store_logo' => Setting::get('store_logo'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:100',
            'store_address' => 'required|string|max:255',
            'store_phone' => 'required|string|max:20',
            'receipt_footer' => 'nullable|string|max:150',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $oldLogo = Setting::get('store_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
            $validated['store_logo'] = $request->file('logo')->store('logos', 'public');
            unset($validated['logo']);
        }

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
                Cache::forget('setting_'.$key);
            }
        }

        return back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
