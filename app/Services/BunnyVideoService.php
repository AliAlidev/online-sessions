<?php

namespace App\Services;

use App\Models\BunnyVideoGuid;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\RequestOptions;
use Throwable;

class BunnyVideoService
{
    protected $apiKey;
    protected $libraryId;
    protected $streamPullZone;
    protected $client;

    public function __construct()
    {
        $setting = getSetting();
        if ($setting) {
            $this->apiKey = isset($setting['video']['video_api_key']) ? $setting['video']['video_api_key'] : null;
            $this->libraryId = isset($setting['video']['video_library_id']) ? $setting['video']['video_library_id'] : null;
            $this->streamPullZone = isset($setting['video']['stream_pull_zone']) ? $setting['video']['stream_pull_zone'] : null;
        }
        $this->client = new Client();
    }

    function removeEmptyVideos()
    {
        BunnyVideoGuid::where('status', 0)->get()->map(function ($video) {
            $this->deleteVideo($video->guid);
            BunnyVideoGuid::where('guid', $video->guid)->delete();
        });
    }

    function addEmptyVideo($guid)
    {
        BunnyVideoGuid::create(['guid' => $guid]);
    }

    function updateEmptyVideoStatus($guid)
    {
        BunnyVideoGuid::where('guid', $guid)->update(['status' => 1]);
    }

    function guarantiedUploadVideo($file, $fileName, $collectionId = null, $resolution = null)
    {
        $guid = null;
        try {
            $this->removeEmptyVideos();
            $fileStream = fopen($file->getPathname(), 'r');
            $headers = [
                'AccessKey' => $this->apiKey,
                'content-type' => 'application/json'
            ];

            // Step 1: Create video entry in BunnyCDN and get GUID
            $body = json_encode(['title' => $fileName, 'collectionId' => $collectionId]);
            $response = $this->client->post("https://video.bunnycdn.com/library/{$this->libraryId}/videos", [
                'headers' => $headers,
                'body' => $body
            ]);

            $guid = json_decode($response->getBody(), true)['guid'];
            $this->addEmptyVideo($guid);
        } catch (Throwable $th) {
            createServerError($th, "guarantiedUploadVideo");
            return ['success' => false, 'message' => 'Upload failed'];
        }

        try {
            $resolution = $resolution ? "?enabledResolutions={$resolution}" : null;
            $response = $this->client->put("https://video.bunnycdn.com/library/{$this->libraryId}/videos/{$guid}". $resolution, [
                'headers' => $headers,
                RequestOptions::BODY => $fileStream
            ]);
        } catch (Throwable $th) {
            $this->deleteVideo($guid);
            createServerError($th, "guarantiedUploadVideo");
            return ['success' => false, 'message' => 'Upload failed'];
        } finally {
            if (is_resource($fileStream)) {
                fclose($fileStream);
            }
        }

        if ($response->getStatusCode() == 200) {
            $this->updateEmptyVideoStatus($guid);
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
        try {
            $client = new Client();
            $headers = [
                'AccessKey' =>  $this->apiKey
            ];
            $request = new Psr7Request('GET', "https://video.bunnycdn.com/library/" . $this->libraryId . "/videos/" . $videoId, $headers);
            $res = $client->sendAsync($request)->wait();
            return json_decode($res->getBody(), true);
        } catch (Throwable $th) {
            createServerError($th, "getVideo");
            return false;
        }
    }

    /**
     * Delete a video from Bunny Stream
     */
    public function deleteVideo(string $videoId)
    {
        try {
            $headers = [
                'AccessKey' => $this->apiKey
            ];
            $request = new Psr7Request('DELETE', "https://video.bunnycdn.com/library/" . $this->libraryId . "/videos/" . $videoId, $headers);
            $res = $this->client->sendAsync($request)->wait();
            if ($res->getStatusCode() == 200)
                return true;
            else
                return false;
        } catch (Throwable $th) {
            createServerError($th, "deleteVideo");
            return false;
        }
    }

    public function deleteMultipleVideos(array $videoIds)
    {
        try {
            $client = new Client();
            $headers = [
                'AccessKey' => $this->apiKey
            ];
            foreach ($videoIds as $key => $videoId) {
                $request = new Psr7Request('DELETE', "https://video.bunnycdn.com/library/" . $this->libraryId . "/videos/" . $videoId, $headers);
                $res = $client->sendAsync($request)->wait();
            }
            return true;
        } catch (Throwable $th) {
            createServerError($th, "deleteMultipleVideos");
            return false;
        }
    }

    /**
     * Get Embed URL for a video
     */
    public function getEmbedUrl(string $videoId)
    {
        return "https://iframe.mediadelivery.net/embed/{$this->libraryId}/{$videoId}";
    }

    function createCollection($name)
    {
        try {
            $client = new Client();
            $headers = [
                'accessKey' => $this->apiKey,
                'content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];
            $json = json_encode([
                'name' => $name
            ]);
            $request = new Psr7Request('POST', 'https://video.bunnycdn.com/library/' . $this->libraryId . '/collections', $headers, $json);
            $res = $client->sendAsync($request)->wait();
            return ['success' => true, 'data' => json_decode($res->getBody(), true)];
        } catch (\Throwable $th) {
            createServerError($th, "createCollection");
            return ['success' => false, 'message' => $th->getMessage()];
        }
    }

    function deleteCollection($collectionId)
    {
        try {
            $client = new Client();
            $headers = [
                'accessKey' => $this->apiKey
            ];
            $request = new Psr7Request('DELETE', 'https://video.bunnycdn.com/library/' . $this->libraryId . '/collections/' . $collectionId, $headers);
            $res = $client->sendAsync($request)->wait();
            return ['success' => true, 'data' => json_decode($res->getBody(), true)];
        } catch (\Throwable $th) {
            createServerError($th, "deleteCollection");
            return ['success' => false, 'message' => $th->getMessage()];
        }
    }
}
