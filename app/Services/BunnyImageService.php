<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Throwable;

class BunnyImageService
{
    private $storageZone;
    private $storageAccessKey;
    private $apiKey;
    private $client;
    private $cdnPullZone;
    private $region;

    public function __construct()
    {
        $setting = getSetting();
        if ($setting) {
            $this->storageZone = isset($setting['image']['storage_zone_name']) ? $setting['image']['storage_zone_name'] : null;
            $this->storageAccessKey = isset($setting['image']['storage_access_token']) ? $setting['image']['storage_access_token'] : null;
            $this->apiKey = isset($setting['global']['api_key']) ? $setting['global']['api_key'] : null;
            $this->cdnPullZone = isset($setting['image']['image_pull_zone']) ? $setting['image']['image_pull_zone'] : null;
            $this->region = isset($setting['image']['region']) ? $setting['image']['region'] : null;
        }
        $this->client = new Client();
    }

    public function GuarantiedUploadImage($file, $folderPath)
    {
        try {
            $url = "https://{$this->region}.bunnycdn.com/{$this->storageZone}/{$folderPath}";
            $fileStream = fopen($file->getPathname(), 'r');
            $headers = [
                'AccessKey' => $this->storageAccessKey,
                'content-type' => 'application/json'
            ];
            try {
                $response = $this->client->put($url, [
                    'headers' => $headers,
                    RequestOptions::BODY => $fileStream
                ]);
            } finally {
                if (is_resource($fileStream)) {
                    fclose($fileStream);
                }
            }

            if ($response->getStatusCode() === 201) {
                return [
                    'success' => true,
                    'path' => "https://{$this->cdnPullZone}.b-cdn.net/{$folderPath}"
                ];
            }

            return ['success' => false, 'message' => 'Upload failed'];
        } catch (\Throwable $e) {
            createServerError($e, "GuarantiedUploadImage");
            return ['success' => false, 'message' => 'Upload failed'];
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
        $url = "https://{$this->region}.bunnycdn.com/{$this->storageZone}/{$directory}";

        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'AccessKey' => $this->storageAccessKey,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (Throwable $e) {
            createServerError($e, "listFiles");
            return [];
        }
    }

    /**
     * Delete a file from Bunny.net storage.
     *
     * @param string $filePath
     * @return bool
     */
    public function deleteFile($file, $withModelData = true)
    {
        if ($withModelData) {
            $path = $file->folder->event->bunny_main_folder_name . '/' . $file->folder->event->bunny_event_name  . '/' . $file->folder->bunny_folder_name . '/' . $file->file_name_with_extension;
            $url = "https://{$this->region}.bunnycdn.com/{$this->storageZone}/{$path}";
        } else {
            $url = "https://{$this->region}.bunnycdn.com/{$this->storageZone}/{$file}";
        }
        try {
            $response = $this->client->delete($url, [
                'headers' => [
                    'AccessKey' => $this->storageAccessKey,
                ],
            ]);
            return $response->getStatusCode() === 200;
        } catch (Throwable $e) {
            createServerError($e, "deleteFile");
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
        } catch (Throwable $e) {
            createServerError($e, "getStorageZoneStatistics");
            return false;
        }
    }

    public function deleteFolder($folderPath)
    {
        // Ensure the folder path ends with a slash
        if (substr($folderPath, -1) !== '/') {
            $folderPath .= '/';
        }

        // List all files in the folder
        $files = $this->listFiles($folderPath);

        if (empty($files)) {
            createServerError(new Exception('No files found in the folder: ' . $folderPath), "deleteFolder");
            return false;
        }

        // Delete each file in the folder
        foreach ($files as $file) {
            $filePath = $folderPath . $file['ObjectName'];

            if (!$this->deleteFile($filePath, false)) {
                createServerError(new Exception('Failed to delete file: ' . $filePath), "deleteFolder");
                return false;
            }
        }

        $this->deleteFolderItSelf($folderPath);
        return true;
    }

    function deleteFolderItSelf($folderPath)
    {
        $client = new Client();

        $headers = [
            'AccessKey' => $this->storageAccessKey,
            'Accept' => 'application/json'
        ];
        $url = "https://{$this->region}.bunnycdn.com/{$this->storageZone}/{$folderPath}";
        $client->delete($url, ['headers' => $headers]);
        return true;
    }
}
