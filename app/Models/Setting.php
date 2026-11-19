<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'logo'];

    public static function get($key, $default = null)
    {
        $cachedSetting = Cache::get('setting_'.$key);

        if ($cachedSetting instanceof self) {
            return $cachedSetting->value;
        }

        $setting = self::where('key', $key)->first();

        if ($setting) {
            Cache::put('setting_'.$key, $setting, 3600);

            return $setting->value;
        }

        return $default;
    }
}
