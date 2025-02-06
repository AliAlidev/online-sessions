<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class BunnyImageService
{
    private $region;
    private $storageZone;
    private $storageAccessKey;
    private $apiKey;
    private $client;
    private $cdnPullZone;

    public function __construct()
    {
        $setting = getSetting();
        if ($setting) {
            $this->region = $setting['image']['storage_region'];
            $this->storageZone = $setting['image']['storage_zone_name'];
            $this->storageAccessKey = $setting['image']['storage_access_token'];
            $this->apiKey = config('services.bunny.api_key');
            $this->cdnPullZone = $setting['image']['image_pull_zone'];
        }
        $this->client = new Client();
    }

    public function createFolder($folderPath)
    {
        // Ensure the folder path ends with a slash
        if (substr($folderPath, -1) !== '/') {
            $folderPath .= '/';
        }

        // Create a placeholder file (e.g., .keep)
        $placeholderFileName = '.keep';
        $placeholderFilePath = $folderPath . $placeholderFileName;

        // Create a temporary empty file
        $tempFile = tmpfile();
        fwrite($tempFile, ''); // Write empty content
        $tempFilePath = stream_get_meta_data($tempFile)['uri'];

        // Upload the placeholder file
        $result = $this->uploadFile($tempFilePath, $placeholderFilePath);

        // Close the temporary file
        fclose($tempFile);

        return $result;
    }

    /**
     * Upload a file to Bunny.net storage.
     *
     * @param string $filePath
     * @param string $uploadPath
     * @return bool
     */
    public function uploadFile($file, $uploadPath)
    {
        $url = "https://{$this->region}.bunnycdn.com/{$this->storageZone}/{$uploadPath}";

        $finalFile = null;
        if (!is_string($file) && $file->isValid()) {
            $finalFile = $file->getPathname();
        } else {
            $finalFile = $file;
        }

        try {
            $response = $this->client->put($url, [
                'headers' => [
                    'AccessKey' => $this->storageAccessKey,
                ],
                'body' => fopen($finalFile, 'r'),
            ]);

            if ($response->getStatusCode() === 201) {
                return "https://{$this->cdnPullZone}.b-cdn.net/{$uploadPath}";
            } else {
                return false;
            }
        } catch (GuzzleException $e) {
            Log::error('Bunny.net Upload Error: ' . $e->getMessage());
            return false;
        }
    }

    public function GuarantiedUploadFile($file, $uploadPath)
    {
        $finalFile = null;
        if (!is_string($file) && $file->isValid()) {
            $finalFile = $file->getPathname();;
        } else {
            $finalFile = $file;
        }
        $url = "https://{$this->region}.bunnycdn.com/{$this->storageZone}/{$uploadPath}";
        $maxRetries = 3;
        $retryDelay = 2;
        $attempts = 0;
        while ($attempts < $maxRetries) {
            try {
                $response = $this->client->put($url, [
                    'headers' => [
                        'AccessKey' => $this->storageAccessKey,
                    ],
                    'body' => fopen($finalFile, 'r')
                ]);
                if ($response->getStatusCode() === 201) {
                    return ['success' => true, 'path' => "https://{$this->cdnPullZone}.b-cdn.net/{$uploadPath}"];
                }
                if ($attempts < $maxRetries - 1) {
                    sleep($retryDelay);
                    $retryDelay *= 2;
                }
            } catch (GuzzleException $e) {
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
        $url = "https://{$this->region}.bunnycdn.com/{$this->storageZone}/{$filePath}";

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

    public function renameFolder($oldFolderPath, $newFolderPath)
    {
        // Ensure the folder paths end with a slash
        if (substr($oldFolderPath, -1) !== '/') {
            $oldFolderPath .= '/';
        }
        if (substr($newFolderPath, -1) !== '/') {
            $newFolderPath .= '/';
        }

        // List all files in the old folder
        $files = $this->listFiles($oldFolderPath);

        if (empty($files)) {
            Log::error('No files found in the folder: ' . $oldFolderPath);
            return false;
        }

        // Move each file to the new folder
        foreach ($files as $file) {
            try {
                $oldFilePath = $oldFolderPath . $file['ObjectName'];
                $newFilePath = $newFolderPath . $file['ObjectName'];

                // Download the file from the old path
                $tempFilePath = tempnam(sys_get_temp_dir(), 'bunny');
                if (!$this->downloadFile($oldFilePath, $tempFilePath)) {
                    Log::error('Failed to download file: ' . $oldFilePath);
                    return false;
                }

                // Upload the file to the new path
                if (!$this->uploadFile($tempFilePath, $newFilePath)) {
                    Log::error('Failed to upload file: ' . $newFilePath);
                    return false;
                }

                // Delete the file from the old path
                if (!$this->deleteFile($oldFilePath)) {
                    Log::error('Failed to delete file: ' . $oldFilePath);
                    return false;
                }

                // Clean up the temporary file
                unlink($tempFilePath);
            } catch (\Throwable $th) {
                Log::error('Failed to copy file: ' . $th->getMessage());
                //throw $th;
            }
        }

        return true;
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
            Log::error('No files found in the folder: ' . $folderPath);
            return false;
        }

        // Delete each file in the folder
        foreach ($files as $file) {
            $filePath = $folderPath . $file['ObjectName'];

            if (!$this->deleteFile($filePath)) {
                Log::error('Failed to delete file: ' . $filePath);
                return false;
            }
        }

        return true;
    }

    /**
     * Download a file from Bunny.net storage.
     *
     * @param string $filePath
     * @param string $destination
     * @return bool
     */
    public function downloadFile($filePath, $destination)
    {
        $url = "https://{$this->region}.bunnycdn.com/{$this->storageZone}/{$filePath}";
        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'AccessKey' => $this->storageAccessKey,
                ],
            ]);

            file_put_contents($destination, $response->getBody());
            return true;
        } catch (GuzzleException $e) {
            Log::error('Bunny.net Download Error: ' . $e->getMessage());
            return false;
        }
    }
}
