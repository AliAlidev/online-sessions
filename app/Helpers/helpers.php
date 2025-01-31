<?php

use App\Models\Event;
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
