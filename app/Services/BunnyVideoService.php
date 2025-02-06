<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BunnyVideoService
{
    protected $apiKey;
    protected $libraryId;
    protected $streamPullZone;

    public function __construct()
    {
        $setting = getSetting();
        if ($setting) {
            $this->apiKey = $setting['video']['video_api_key'];
            $this->libraryId = $setting['video']['video_library_id'];
            $this->streamPullZone = $setting['video']['stream_pull_zone'];
        }
    }

    public function GuarantiedUploadVideo($file)
    {
        $maxRetries = 3;
        $retryDelay = 2;
        $attempts = 0;
        while ($attempts < $maxRetries) {
            try {
                $client = new Client();
                $headers = [
                    'AccessKey' => $this->apiKey,
                    'content-type' => 'application/json'
                ];
                $body = '{
                   "title": "' . $file->getClientOriginalName() . '"
                }';
                $request = new Psr7Request('POST', 'https://video.bunnycdn.com/library/379365/videos', $headers, $body);
                $res = $client->sendAsync($request)->wait();
                $guid = json_decode($res->getBody(), true)['guid'];

                //////////////
                $body = $file->get();
                $request = new Psr7Request('PUT', 'https://video.bunnycdn.com/library/379365/videos/' . $guid, $headers, $body);
                $res = $client->sendAsync($request)->wait();
                if ($res->getStatusCode() == 200) {
                    return ['success' => true, 'path' => "https://video.bunnycdn.com/play/" . $this->libraryId . "/" . $guid, 'guid' => $guid];
                }
                if ($attempts < $maxRetries - 1) {
                    sleep($retryDelay);
                    $retryDelay *= 2;
                }
            } catch (GuzzleException $e) {
                Log::channel("bunny")->alert($e->getMessage());
                if ($attempts < $maxRetries - 1) {
                    sleep($retryDelay);
                    $retryDelay *= 2;
                }
            }
            $attempts++;
        }
        return ['success' => false, 'path' => null];
    }

    /**
     * Get video details by ID
     */
    public function getVideo(string $videoId)
    {
        $client = new Client();
        $headers = [
            'AccessKey' =>  $this->apiKey
        ];
        $request = new Psr7Request('GET', "https://video.bunnycdn.com/library/" . $this->libraryId . "/videos/" . $videoId, $headers);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody(), true);
    }

    /**
     * Delete a video from Bunny Stream
     */
    public function deleteVideo(string $videoId)
    {
        $client = new Client();
        $headers = [
            'AccessKey' => $this->apiKey
        ];
        $request = new Psr7Request('DELETE', "https://video.bunnycdn.com/library/" . $this->libraryId . "/videos/" . $videoId, $headers);
        $res = $client->sendAsync($request)->wait();
        if ($res->getStatusCode() == 200)
            return true;
        else
            return false;
    }

    /**
     * Get Embed URL for a video
     */
    public function getEmbedUrl(string $videoId)
    {
        return "https://iframe.mediadelivery.net/embed/{$this->libraryId}/{$videoId}";
    }
}
