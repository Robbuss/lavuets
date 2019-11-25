<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        return Setting::all()->map(function ($q) {
            return [
                'key' => $q->key,
                'value' => $q->value,
            ];
        });
    }

    public function update(Request $request)
    {
        foreach ($request->settings as $setting) {
            Setting::where('key', $setting['key'])->update([
                'value' => $setting['value']
            ]);
        }
        return ["success" => true];
    }

    public function layout()
    {
        return Setting::public()->get()->map(function ($q) {
            return ['key' => $q->key, 'value' => $q->value];
        });
    }
}
