<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Request;

class InsightController extends Controller
{
    function index()
    {
        $events = getEventsIncreasingCount();
        $clients = getClientsIncreasingCount();
        $results = $this->getStatisticsForEachStorageZone();

        $sections = [];
        foreach ($results as $key => $result) {
            if(isset($result['statistics'])){
                $sections[] = [
                    'name' => $result['pull_zone_name'],
                    'prefix' => $result['prefix'],
                    'charts' => [
                        [
                            'name' => 'TotalBandwidthUsed',
                            'value' => $this->formatBytes($result['statistics']['TotalBandwidthUsed'])
                        ],
                        [
                            'name' => 'TotalOriginTraffic',
                            'value' => $this->formatBytes($result['statistics']['TotalOriginTraffic'])
                        ],
                        [
                            'name' => 'AverageOriginResponseTime',
                            'value' => $this->formatResponseTime($result['statistics']['AverageOriginResponseTime'])
                        ],
                        [
                            'name' => 'TotalRequestsServed',
                            'value' => $result['statistics']['TotalRequestsServed']
                        ],
                        [
                            'name' => 'CacheHitRate',
                            'value' => $this->getCacheHitRateValue($result['statistics']['CacheHitRate'])
                        ]
                    ]
                ];
            }
        }

        return view('dashboard.insight.index', [
            'events_percentage_value' => $events['percentage'],
            'events_percentage_color' => $events['color'],
            'events_percentage_sign' => $events['sign'],
            'clients_percentage_value' => $clients['percentage'],
            'clients_percentage_color' => $clients['color'],
            'clients_percentage_sign' => $clients['sign'],
            'sections' => $sections
        ]);
    }

    function getCacheHitRateValue($ratio)
    {
        return round($ratio, 2) . ' %';
    }

    function formatResponseTime($milliseconds)
    {
        if ($milliseconds >= 1000) {
            return round($milliseconds / 1000, 2) . ' seconds';
        }
        return $milliseconds . ' ms';
    }

    function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    function getBunnyStatistics($storageZone = -1)
    {
        try {
            $client = new Client();
            $setting = getSetting()['global'];
            $headers = [
                'AccessKey' => $setting['api_key']
            ];
            $request = new Psr7Request('GET', 'https://api.bunny.net/statistics?pullZone=' . $storageZone . '&hourly=true', $headers);
            $res = $client->sendAsync($request)->wait();
            return json_decode($res->getBody(), true);
        } catch (\Throwable $th) {
            return null;
        }
    }

    function getStorageZonesList()
    {
        $client = new Client();
        $setting = getSetting()['global'];
        $headers = [
            'AccessKey' => $setting['api_key']
        ];
        $request = new Psr7Request('GET', 'https://api.bunny.net/storagezone?page=0&perPage=1000', $headers);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody(), true);
    }

    function getPullZoneIdFromName($name)
    {
        try {
            $client = new Client();
            $setting = getSetting()['global'];
            $headers = [
                'AccessKey' => $setting['api_key']
            ];
            $request = new Psr7Request('GET', 'https://api.bunny.net/pullzone?search=' . $name, $headers);
            $res = $client->sendAsync($request)->wait();
            return json_decode($res->getBody(), true)['Items'][0]['Id'];
        } catch (\Throwable $th) {
            return null;
        }
    }

    function getStatisticsForEachStorageZone()
    {
        // foreach ($this->getStorageZonesList() as $key => $storage) {
        //     foreach ($storage['PullZones'] as $key => $pullZone) {
        //         $statistics = $this->getBunnyStatistics($pullZone['Id']);
        //         $results[] = [
        //             'pull_zone_name' => $pullZone['Name']. ' Storage Zone Statistics',
        //             'statistics' => $statistics
        //         ];
        //     }
        // }
        $results = [];
        $setting = getSetting()['image'];
        if ($setting) {
            $statistics = $this->getBunnyStatistics($this->getPullZoneIdFromName($setting['image_pull_zone']));
            $results[] = [
                'pull_zone_name' => $setting['image_pull_zone'],
                'prefix' => ' Image Pull Zone Statistics',
                'statistics' => $statistics
            ];
            $setting = getSetting()['video'];
            $statistics = $this->getBunnyStatistics($setting['stream_pull_zone']);
            $results[] = [
                'pull_zone_name' => $setting['stream_pull_zone'],
                'prefix' => ' Video Pull Zone Statistics',
                'statistics' => $statistics
            ];
        }
        return $results;
    }
}
