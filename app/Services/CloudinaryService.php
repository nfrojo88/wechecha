<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CloudinaryService
{
    protected $cloudName;
    protected $apiKey;
    protected $apiSecret;

    public function __construct()
    {
        $this->cloudName = env('CLOUDINARY_CLOUD_NAME', 'dg1ijsqx6');
        $this->apiKey    = env('CLOUDINARY_API_KEY', '748361957149737');
        $this->apiSecret = env('CLOUDINARY_API_SECRET', 'o_1uVvfylOqYY0t4QzP9JeLzPo4');
    }

    /**
     * Upload a file to Cloudinary and return its secure URL.
     * Fallbacks to local disk storage using Storage::put if upload fails or is unavailable.
     *
     * @param UploadedFile|string $file
     * @param string $folder
     * @param string $fallbackDisk
     * @return string
     */
    public function upload($file, string $folder = 'hr_documents', string $fallbackDisk = 'public'): string
    {
        if (!$file) {
            return '';
        }

        $content = null;
        $fileName = 'file';

        try {
            if ($file instanceof UploadedFile) {
                $filePath = $file->getRealPath();
                $fileName = $file->getClientOriginalName();
                if ($filePath && file_exists($filePath)) {
                    $content = file_get_contents($filePath);
                }
            } elseif (is_string($file) && file_exists($file)) {
                $filePath = $file;
                $fileName = basename($file);
                $content = file_get_contents($filePath);
            }

            if (!empty($content) && !empty($this->cloudName) && !empty($this->apiKey) && !empty($this->apiSecret)) {
                $timestamp = time();
                $paramsToSign = "folder={$folder}&timestamp={$timestamp}";
                $signature = sha1($paramsToSign . $this->apiSecret);

                $response = Http::withoutVerifying()->timeout(15)->attach(
                    'file',
                    $content,
                    $fileName
                )->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/auto/upload", [
                    'api_key'   => $this->apiKey,
                    'timestamp' => $timestamp,
                    'folder'    => $folder,
                    'signature' => $signature,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $url = $data['secure_url'] ?? $data['url'] ?? null;
                    if ($url) {
                        return $url;
                    }
                }

                Log::warning('Cloudinary Upload API non-200 response: ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('Cloudinary Upload Exception: ' . $e->getMessage());
        }

        // Centralized Local Fallback to public/uploads/{folder}/
        return FileUploadService::upload($file, $folder, false);
    }

    /**
     * Attempt upload to Cloudinary only, without local fallback.
     */
    public function uploadToCloudinaryOnly($file, string $folder = 'hr_documents'): ?string
    {
        if (!$file || empty($this->cloudName) || empty($this->apiKey) || empty($this->apiSecret)) {
            return null;
        }

        $content = null;
        $fileName = 'file';

        try {
            if ($file instanceof UploadedFile) {
                $filePath = $file->getRealPath();
                $fileName = $file->getClientOriginalName();
                if ($filePath && file_exists($filePath)) {
                    $content = file_get_contents($filePath);
                }
            } elseif (is_string($file) && file_exists($file)) {
                $filePath = $file;
                $fileName = basename($file);
                $content = file_get_contents($filePath);
            }

            if (!empty($content)) {
                $timestamp = time();
                $paramsToSign = "folder={$folder}&timestamp={$timestamp}";
                $signature = sha1($paramsToSign . $this->apiSecret);

                $response = Http::withoutVerifying()->timeout(15)->attach(
                    'file',
                    $content,
                    $fileName
                )->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/auto/upload", [
                    'api_key'   => $this->apiKey,
                    'timestamp' => $timestamp,
                    'folder'    => $folder,
                    'signature' => $signature,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['secure_url'] ?? $data['url'] ?? null;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Cloudinary Upload failed: ' . $e->getMessage());
        }

        return null;
    }
}
