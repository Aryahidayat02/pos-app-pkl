<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Support\ImageService;
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
            'show_logo_on_receipt' => Setting::get('show_logo_on_receipt', '1') === '1',
            'low_stock_alert' => Setting::get('low_stock_alert', '1') === '1',
            'default_payment_method' => Setting::get('default_payment_method', 'tunai'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request, ImageService $imageService): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:100',
            'store_address' => 'required|string|max:255',
            'store_phone' => 'required|string|max:20',
            'receipt_footer' => 'nullable|string|max:150',
            'show_logo_on_receipt' => 'nullable|in:1',
            'low_stock_alert' => 'nullable|in:1',
            'default_payment_method' => 'required|in:tunai,qris,transfer',
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'extensions:jpg,jpeg,png', 'max:2048'],
        ]);

        $validated['show_logo_on_receipt'] = $request->boolean('show_logo_on_receipt') ? '1' : '0';
        $validated['low_stock_alert'] = $request->boolean('low_stock_alert') ? '1' : '0';

        $oldLogo = null;
        if ($request->hasFile('logo')) {
            $oldLogo = Setting::get('store_logo');
            $validated['store_logo'] = $imageService->storeCover($request->file('logo'), 'logos');
            unset($validated['logo']);
        }

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
                Cache::forget('setting_'.$key);
            }
        }

        if ($oldLogo && ! filter_var($oldLogo, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($oldLogo);
        }

        return back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
