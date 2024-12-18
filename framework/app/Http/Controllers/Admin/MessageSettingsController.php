<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Model\ChatSettingsModel;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class MessageSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        // dd(config('broadcasting.pusher'));
        return view("utilities.chat_settings");
    }
    public function store(Request $request)
    {
        ChatSettingsModel::where('name', 'pusher_app_id')->update(['value' => $request->pusher_app_id]);
        ChatSettingsModel::where('name', 'pusher_app_key')->update(['value' => $request->pusher_app_key]);
        ChatSettingsModel::where('name', 'pusher_app_secret')->update(['value' => $request->pusher_app_secret]);
        ChatSettingsModel::where('name', 'pusher_app_cluster')->update(['value' => $request->pusher_app_cluster]);

        $this->setEnvValue('PUSHER_APP_ID', $request->pusher_app_id ?? "");
        $this->setEnvValue('PUSHER_APP_KEY', $request->pusher_app_key ?? "");
        $this->setEnvValue('PUSHER_APP_SECRET', $request->pusher_app_secret ?? "");
        $this->setEnvValue('PUSHER_APP_CLUSTER', $request->pusher_app_cluster ?? "");

        return back()->with(['msg' => __('fleet.chat_settingsUpdated')]);
    }

    /**
     * @param string $key
     * @param string $value
     */
    protected function setEnvValue(string $key, string $value)
    {
        $path = app()->environmentFilePath();
        $env = file_get_contents($path);

        $old_value = env($key);

        if (!str_contains($env, $key . '=')) {
            $env .= sprintf("%s=%s\n", $key, $value);
        } else if ($old_value) {
            $env = str_replace(sprintf('%s=%s', $key, $old_value), sprintf('%s=%s', $key, $value), $env);
        } else {
            $env = str_replace(sprintf('%s=', $key), sprintf('%s=%s', $key, $value), $env);
        }
        // Reload the cached config
        if (file_exists(App::getCachedConfigPath())) {
            Artisan::call("config:cache");
        }
        
        file_put_contents($path, $env);
    }

    // public function setEnvironmentValue2(array $values)
    // {

    //     $envFile = app()->environmentFilePath();
    //     $str = file_get_contents($envFile);

    //     if (count($values) > 0) {
    //         foreach ($values as $envKey => $envValue) {

    //             $str .= "\n"; // In case the searched variable is in the last line without \n
    //             $keyPosition = strpos($str, "{$envKey}=");
    //             $endOfLinePosition = strpos($str, "\n", $keyPosition);
    //             $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

    //             // If key does not exist, add it
    //             if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
    //                 $str .= "{$envKey}={$envValue}\n";
    //             } else {
    //                 $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
    //             }

    //         }
    //     }

    //     $str = substr($str, 0, -1);
    //     if (!file_put_contents($envFile, $str)) {
    //         return false;
    //     }

    //     // Reload the cached config
    //     if (file_exists(App::getCachedConfigPath())) {
    //         Artisan::call("config:cache");
    //     }

    //     return true;

    // }
}
