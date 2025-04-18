<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Request;

class InsightController extends Controller
{
    function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $request->replace(['start' => Carbon::parse($request->start)->startOfDay()->toDateTimeString(), 'end' => Carbon::parse($request->end)->endOfDay()->toDateTimeString()]);
                $events = getEventsIncreasingCount($request);
                $clients = getClientsIncreasingCount($request);
                $results = $this->getStatisticsForEachStorageZone($request);

                $sections = [];
                foreach ($results as $key => $result) {
                    if (isset($result['statistics'])) {
                        $sections[] = [
                            'name' => $result['pull_zone_name'],
                            'prefix' => $result['prefix'],
                            'charts' => [
                                [
                                    'name' => 'TotalBandwidthUsed',
                                    'value' => formatBytes($result['statistics']['TotalBandwidthUsed'])
                                ],
                                [
                                    'name' => 'TotalOriginTraffic',
                                    'value' => formatBytes($result['statistics']['TotalOriginTraffic'])
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
                return view('dashboard.insight.insights', [
                    'events_percentage_value' => $events['percentage'],
                    'events_percentage_color' => $events['color'],
                    'events_percentage_sign' => $events['sign'],
                    'clients_percentage_value' => $clients['percentage'],
                    'clients_percentage_color' => $clients['color'],
                    'clients_percentage_sign' => $clients['sign'],
                    'sections' => $sections,
                    'request' => $request
                ]);
            }
            return view('dashboard.insight.index');
        } catch (Exception $th) {
            createServerError($th, "indexInsights", "insights");
            return false;
        }
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

    function getBunnyStatistics($storageZone = -1, $request = null)
    {
        try {
            $client = new Client();
            $setting = getSetting()['global'];
            $headers = [
                'AccessKey' => $setting['api_key']
            ];
            if ($request && $request->start && $request->end) {
                $startDate = Carbon::parse($request->start)->format('m/d/Y');
                $endDate = Carbon::parse($request->end)->format('m/d/Y');
            } else {
                $startDate = Carbon::now()->startOfDay()->subMonth()->format('m/d/Y');
                $endDate = Carbon::now()->format('m/d/Y');
            }
            $request = new Psr7Request('GET', 'https://api.bunny.net/statistics?pullZone=' . $storageZone . '&hourly=false&dateFrom=' . $startDate . '&dateTo=' . $endDate, $headers);
            $res = $client->sendAsync($request)->wait();
            return json_decode($res->getBody(), true);
        } catch (Exception $th) {
            createServerError($th, "getBunnyStatistics", "insights");
            return null;
        }
    }

    function getStorageZonesList()
    {
        try {
            $client = new Client();
            $setting = getSetting()['global'];
            $headers = [
                'AccessKey' => $setting['api_key']
            ];
            $request = new Psr7Request('GET', 'https://api.bunny.net/storagezone?page=0&perPage=1000', $headers);
            $res = $client->sendAsync($request)->wait();
            return json_decode($res->getBody(), true);
        } catch (Exception $th) {
            createServerError($th, "getStorageZonesList", "insights");
            return null;
        }
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
        } catch (Exception $th) {
            createServerError($th, "getPullZoneIdFromName", "insights");
            return null;
        }
    }

    function getStatisticsForEachStorageZone($request = null)
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
        try {
            $results = [];
            $setting = getSetting()['image'];
            if ($setting) {
                $statistics = $this->getBunnyStatistics($this->getPullZoneIdFromName($setting['image_pull_zone']), $request);
                $results[] = [
                    'pull_zone_name' => $setting['image_pull_zone'],
                    'prefix' => ' Image Pull Zone Statistics',
                    'statistics' => $statistics
                ];
                $setting = getSetting()['video'];
                $statistics = $this->getBunnyStatistics($setting['stream_pull_zone'], $request);
                $results[] = [
                    'pull_zone_name' => $setting['stream_pull_zone'],
                    'prefix' => ' Video Pull Zone Statistics',
                    'statistics' => $statistics
                ];
            }
            return $results;
        } catch (Exception $th) {
            createServerError($th, "getStatisticsForEachStorageZone", "insights");
            return null;
        }
    }
}
