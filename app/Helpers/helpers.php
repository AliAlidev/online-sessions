<?php

use App\Models\Client;
use App\Models\Event;
use App\Models\Setting;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Psr7\Request;
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
        if ($current > $previous) {
            $previous = $previous == 0 ? 1 : $previous;
            $percentage = abs(($current - $previous) / $previous) * 100;
            $color =  '#68c790';
            $sign = '+';
        } else if ($current < $previous) {
            $current = $current == 0 ? 1 : $current;
            $percentage = abs(($previous - $current) / $current) * 100;
            $color =  'red';
            $sign = '-';
        } else {
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
        if ($current > $previous) {
            $previous = $previous == 0 ? 1 : $previous;
            $percentage = abs(($current - $previous) / $previous) * 100;
            $color =  '#68c790';
            $sign = '+';
        } else if ($current < $previous) {
            $current = $current == 0 ? 1 : $current;
            $percentage = abs(($previous - $current) / $current) * 100;
            $color =  'red';
            $sign = '-';
        } else {
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
        return [
            'global' => $settingGlobal?->data[0],
            'image' => $settingImage?->data[0],
            'image_setting_id' => $settingImage?->id,
            'video' => $settingVideo?->data[0],
            'video_setting_id' => $settingVideo?->id,
        ];
    }
}

if (!function_exists('checkImageConfig')) {
    function checkImageConfig($config)
    {
        $accessToken = isset($config) ? $config['storage_access_token'] : null;
        $storageName = isset($config) ? $config['storage_zone_name'] : null;
        $client = new GuzzleHttpClient();
        $headers = [
            'accessKey' => $accessToken
        ];
        $request = new Request('GET', 'https://storage.bunnycdn.com/' . $storageName . '/*', $headers);
        try {
            $response = $client->send($request, ['headers' => $headers]);
            $statusCode = $response->getStatusCode(); // Get the HTTP status code
            return $statusCode == 401 ? false : true;
        } catch (Exception $e) {
            if ($e->getResponse()->getStatusCode() == 401)
                return false;
            return true;
        }
    }
}

if (!function_exists('checkVideoConfig')) {
    function checkVideoConfig($config)
    {
        $accessToken = isset($config) ? $config['video_api_key'] : null;
        $videoLibraryId = isset($config) ? $config['video_library_id'] : null;
        $client = new GuzzleHttpClient();
        $headers = [
            'AccessKey' => $accessToken
        ];
        $request = new Request('GET', 'https://video.bunnycdn.com/library/' . $videoLibraryId . '/videos', $headers);
        try {
            $response = $client->send($request, ['headers' => $headers]);
            $statusCode = $response->getStatusCode(); // Get the HTTP status code
            return $statusCode == 200 ? true : false;
        } catch (Exception $e) {
            if ($e->getResponse()->getStatusCode() == 200)
                return true;
            return false;
        }
    }
}

if (!function_exists('checkApiKey')) {
    function checkApiKey($apiKey)
    {
        $client = new GuzzleHttpClient();
        $headers = [
            'AccessKey' => $apiKey
        ];
        $request = new Request('GET', 'https://api.bunny.net/apikey', $headers);
        try {
            $response = $client->send($request, ['headers' => $headers]);
            $statusCode = $response->getStatusCode();
            return $statusCode == 200 ? true : false;
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('checkPullZoneAvailability')) {
    function checkPullZoneAvailability($apiKey, $pullZoneName)
    {
        $client = new GuzzleHttpClient();
        $headers = [
            'AccessKey' => $apiKey,
            'content-type' => 'application/json'
        ];
        $body = '{
           "Name": "' . $pullZoneName . '"
        }';
        $request = new Request('POST', 'https://api.bunny.net/storagezone/checkavailability', $headers, $body);
        try {
            $response = $client->send($request, ['headers' => $headers]);
            $statusCode = json_decode($response->getBody(), true);
            if (isset($statusCode['Available']) && $statusCode['Available'])
                return true;
            else
                return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
