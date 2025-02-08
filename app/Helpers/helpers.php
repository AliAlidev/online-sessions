<?php

use App\Models\Client;
use App\Models\Event;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (!function_exists('uploadFile')) {
    function uploadFile($file, $path)
    {
        $path = Str::of($path)->replace(' ', '')->replace('-', '_');
        $fileNameToStore = $path . '/';
        $uploadedPath = Storage::disk('public')->put($fileNameToStore, $file);
        $uploadedPath = Storage::disk('public')->url($uploadedPath);
        $fileNameToStore = $path . '/' . basename($uploadedPath);
        return $fileNameToStore;
    }
}

if (!function_exists('deleteFile')) {
    function deleteFile($path)
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }
}

if (!function_exists('uploadBase64File')) {
    function uploadBase64File($base64File, $path)
    {
        $qrCodeBase64 = str_replace('data:image/png;base64,', '', $base64File);
        $qrCodeBase64 = str_replace(' ', '+', $qrCodeBase64);
        $file = base64_decode($qrCodeBase64);
        $path = Str::of($path)->replace(' ', '')->replace('-', '_');
        $fileNameToStore = $path . '/qrcode_' . time() . '.png';
        $uploadedPath = Storage::disk('public')->put($fileNameToStore, $file);
        $uploadedPath = Storage::disk('public')->url($uploadedPath);
        return $fileNameToStore;
    }
}

if (!function_exists('getEventsCount')) {
    function getEventsCount()
    {
        return Event::count();
    }
}

if (!function_exists('getClientsCount')) {
    function getClientsCount()
    {
        return Client::count();
    }
}

if (!function_exists('getEventsIncreasingCount')) {
    function getEventsIncreasingCount()
    {
        $compare_start_date = now()->subMonths(2)->subDay()->startOfDay()->toDateTimeString();
        $compare_end_date = now()->subMonth()->subDay()->endOfDay()->toDateTimeString();;
        $start_date = now()->subMonth()->startOfDay()->toDateTimeString();
        $end_date = now()->toDateTimeString();

        $previous = Event::whereBetween('created_at', [$compare_start_date, $compare_end_date])->count();
        $current = Event::whereBetween('created_at', [$start_date, $end_date])->count();
        if ( $current > $previous) {
            $previous = $previous == 0 ? 1 : $previous;
            $percentage = abs(($current - $previous) / $previous) * 100;
            $color =  '#68c790';
            $sign = '+';
        } else if ( $current < $previous) {
            $current = $current == 0 ? 1 : $current;
            $percentage = abs(($previous - $current) / $current) * 100;
            $color =  'red';
            $sign = '-';
        }else{
            $percentage = 0;
            $color =  '#68c790';
            $sign = '';
        }
        return ['percentage' => $percentage, 'color' => $color, 'sign' => $sign];
    }
}

if (!function_exists('getClientsIncreasingCount')) {
    function getClientsIncreasingCount()
    {
        $compare_start_date = now()->subMonths(2)->subDay()->startOfDay()->toDateTimeString();
        $compare_end_date = now()->subMonth()->subDay()->endOfDay()->toDateTimeString();;
        $start_date = now()->subMonth()->startOfDay()->toDateTimeString();
        $end_date = now()->toDateTimeString();

        $previous = client::whereBetween('created_at', [$compare_start_date, $compare_end_date])->count();
        $current = client::whereBetween('created_at', [$start_date, $end_date])->count();
        if ( $current > $previous) {
            $previous = $previous == 0 ? 1 : $previous;
            $percentage = abs(($current - $previous) / $previous) * 100;
            $color =  '#68c790';
            $sign = '+';
        } else if ( $current < $previous) {
            $current = $current == 0 ? 1 : $current;
            $percentage = abs(($previous - $current) / $current) * 100;
            $color =  'red';
            $sign = '-';
        }else{
            $percentage = 0;
            $color =  '#68c790';
            $sign = '';
        }
        return ['percentage' => $percentage, 'color' => $color, 'sign' => $sign];
    }
}

if (!function_exists('getSetting')) {
    function getSetting()
    {
        $settingGlobal = Setting::where('type', 'global')->first();
        $settingImage = Setting::where('type', 'image')->first();
        $settingVideo = Setting::where('type', 'video')->first();
        return[
            'global' => $settingGlobal?->data[0],
            'image' => $settingImage?->data[0],
            'video' => $settingVideo?->data[0]
        ];
    }
}
