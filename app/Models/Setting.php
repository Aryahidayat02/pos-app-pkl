<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get($key, $default = null)
    {
        // Cek cache dulu
        $cachedSetting = Cache::get('setting_' . $key);

        // Kalau cache-nya ada dan bentuknya objek, langsung balikin
        if ($cachedSetting instanceof self) {
            return $cachedSetting->value;
        }

        // Kalau gak ada di cache, ambil dari database
        $setting = self::where('key', $key)->first();

        if ($setting) {
            // Simpan ke cache
            Cache::put('setting_' . $key, $setting, 3600); // Simpan 1 jam
            return $setting->value;
        }

        return $default;
    }
}