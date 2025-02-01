<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class BunnyService
{
    private $storageZone;
    private $storageAccessKey;
    private $apiKey;
    private $client;

    public function __construct()
    {
        $this->storageZone = config('bunny.storage_zone');
        $this->storageAccessKey = config('bunny.storage_access_key');
        $this->apiKey = config('bunny.api_key');
        $this->client = new Client();
    }

    /**
     * Upload a file to Bunny.net storage.
     *
     * @param string $filePath
     * @param string $uploadPath
     * @return bool
     */
    public function uploadFile($filePath, $uploadPath)
    {
        $url = "https://{$this->storageZone}.b-cdn.net/{$uploadPath}";

        try {
            $response = $this->client->put($url, [
                'headers' => [
                    'AccessKey' => $this->storageAccessKey,
                ],
                'body' => fopen($filePath, 'r'),
            ]);

            return $response->getStatusCode() === 201;
        } catch (GuzzleException $e) {
            Log::error('Bunny.net Upload Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * List files in a directory.
     *
     * @param string $directory
     * @return array
     */
    public function listFiles($directory = '')
    {
        $url = "https://{$this->storageZone}.b-cdn.net/{$directory}";

        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'AccessKey' => $this->storageAccessKey,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            Log::error('Bunny.net List Files Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Delete a file from Bunny.net storage.
     *
     * @param string $filePath
     * @return bool
     */
    public function deleteFile($filePath)
    {
        $url = "https://{$this->storageZone}.b-cdn.net/{$filePath}";

        try {
            $response = $this->client->delete($url, [
                'headers' => [
                    'AccessKey' => $this->storageAccessKey,
                ],
            ]);

            return $response->getStatusCode() === 200;
        } catch (GuzzleException $e) {
            Log::error('Bunny.net Delete File Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a pull zone.
     *
     * @param string $name
     * @param string $originUrl
     * @return array|false
     */
    public function createPullZone($name, $originUrl)
    {
        $url = 'https://api.bunny.net/pullzone';

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'AccessKey' => $this->apiKey,
                ],
                'json' => [
                    'Name' => $name,
                    'OriginUrl' => $originUrl,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            Log::error('Bunny.net Create Pull Zone Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * List all pull zones.
     *
     * @return array
     */
    public function listPullZones()
    {
        $url = 'https://api.bunny.net/pullzone';

        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'AccessKey' => $this->apiKey,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            Log::error('Bunny.net List Pull Zones Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Delete a pull zone.
     *
     * @param int $pullZoneId
     * @return bool
     */
    public function deletePullZone($pullZoneId)
    {
        $url = "https://api.bunny.net/pullzone/{$pullZoneId}";

        try {
            $response = $this->client->delete($url, [
                'headers' => [
                    'AccessKey' => $this->apiKey,
                ],
            ]);

            return $response->getStatusCode() === 204;
        } catch (GuzzleException $e) {
            Log::error('Bunny.net Delete Pull Zone Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Purge cache for a pull zone.
     *
     * @param int $pullZoneId
     * @return bool
     */
    public function purgePullZoneCache($pullZoneId)
    {
        $url = "https://api.bunny.net/pullzone/{$pullZoneId}/purgeCache";

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'AccessKey' => $this->apiKey,
                ],
            ]);

            return $response->getStatusCode() === 204;
        } catch (GuzzleException $e) {
            Log::error('Bunny.net Purge Cache Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get storage zone statistics.
     *
     * @param int $storageZoneId
     * @return array|false
     */
    public function getStorageZoneStatistics($storageZoneId)
    {
        $url = "https://api.bunny.net/storagezone/{$storageZoneId}/statistics";

        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'AccessKey' => $this->apiKey,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            Log::error('Bunny.net Storage Zone Statistics Error: ' . $e->getMessage());
            return false;
        }
    }
}
