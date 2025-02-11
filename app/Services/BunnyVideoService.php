<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Cache;

class BunnyVideoService
{
    protected $apiKey;
    protected $libraryId;
    protected $streamPullZone;

    public function __construct()
    {
        $setting = getSetting();
        if ($setting) {
            $this->apiKey = isset($setting['video']['video_api_key']) ? $setting['video']['video_api_key'] : null;
            $this->libraryId = isset($setting['video']['video_library_id']) ? $setting['video']['video_library_id'] : null;
            $this->streamPullZone = isset($setting['video']['stream_pull_zone']) ? $setting['video']['stream_pull_zone'] : null;
        }
    }

    function guarantiedUploadVideo($filePath, $fileName, $uploadId, $fileSize)
    {
        Cache::put("upload_progress_" . $uploadId, 0, 600); // Store initial progress (0%) for 30 minutes
        $client = new Client();
        $headers = [
            'AccessKey' => $this->apiKey,
            'content-type' => 'application/json'
        ];

        // Step 1: Create video entry in BunnyCDN and get GUID
        $body = json_encode(['title' => $fileName]);
        $response = $client->post("https://video.bunnycdn.com/library/{$this->libraryId}/videos", [
            'headers' => $headers,
            'Content-Length' => $fileSize,
            'body' => $body
        ]);

        $guid = json_decode($response->getBody(), true)['guid'];

        // Step 2: Upload the file with progress tracking
        $fileStream = Utils::streamFor(Utils::tryFopen($filePath, 'r'));
        if (!$fileStream) {
            return ['success' => false, 'message' => 'Failed to open file for reading.'];
        }

        try {
            $response = $client->put("https://video.bunnycdn.com/library/{$this->libraryId}/videos/{$guid}", [
                'headers' => $headers,
                RequestOptions::BODY => $fileStream,
                RequestOptions::PROGRESS => function ($downloadTotal, $downloaded, $uploadTotal, $uploaded) use ($uploadId, $fileSize) {
                    $progress = round(($uploaded / $fileSize) * 100, 2);
                    if ($progress > 100)
                        $progress = 100;
                    Cache::put("upload_progress_" . $uploadId, $progress, 600);
                },
                'curl' => [
                    CURLOPT_NOPROGRESS => false, // Enable progress tracking
                ]
            ]);
        } finally {
            if (is_resource($fileStream)) {
                fclose($fileStream);
            }
        }

        if ($response->getStatusCode() == 200) {
            return [
                'success' => true,
                'path' => "https://video.bunnycdn.com/play/{$this->libraryId}/{$guid}",
                'guid' => $guid
            ];
        }

        return ['success' => false, 'message' => 'Upload failed'];
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
        try {
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
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function deleteMultipleVideos(array $videoIds)
    {
        $client = new Client();
        $headers = [
            'AccessKey' => $this->apiKey
        ];
        foreach ($videoIds as $key => $videoId) {
            $request = new Psr7Request('DELETE', "https://video.bunnycdn.com/library/" . $this->libraryId . "/videos/" . $videoId, $headers);
            $res = $client->sendAsync($request)->wait();
        }
        return true;
    }

    /**
     * Get Embed URL for a video
     */
    public function getEmbedUrl(string $videoId)
    {
        return "https://iframe.mediadelivery.net/embed/{$this->libraryId}/{$videoId}";
    }
}
