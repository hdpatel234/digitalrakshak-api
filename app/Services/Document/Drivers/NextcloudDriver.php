<?php

namespace App\Services\Document\Drivers;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class NextcloudDriver extends AbstractDocumentDriver
{
    public function uploadDocument(string $path, string $contents, array $options = []): array
    {
        $response = $this->webdavRequest('put', $this->buildStoragePath($path), [
            'body' => $contents,
            'headers' => $options['headers'] ?? [],
        ]);

        return [
            'success' => true,
            'status' => $response->status(),
            'path' => $path,
        ];
    }

    public function getDocument(string $path): array
    {
        $response = $this->webdavRequest('get', $this->buildStoragePath($path));

        return [
            'success' => true,
            'status' => $response->status(),
            'path' => $path,
            'contents' => $response->body(),
            'content_type' => $response->header('Content-Type'),
            'content_length' => $response->header('Content-Length'),
        ];
    }

    public function deleteDocument(string $path): array
    {
        $response = $this->webdavRequest('delete', $this->buildStoragePath($path));

        return [
            'success' => true,
            'status' => $response->status(),
            'path' => $path,
        ];
    }

    public function createShareLink(string $path, array $options = []): array
    {
        $baseUrl = rtrim((string) $this->requireConfig('api_url'), '/');
        $username = (string) $this->requireConfig('username');
        $password = (string) $this->requireConfig('password');
        $timeout = (int) $this->additionalConfig('timeout', 30);

        $payload = array_filter([
            'path' => $this->sharePath($path),
            'shareType' => 3,
            'permissions' => $options['permissions'] ?? 1,
            'expireDate' => $options['expire_date'] ?? null,
            'password' => $options['password'] ?? null,
        ], static fn ($value) => $value !== null && $value !== '');

        $response = Http::timeout($timeout)
            ->acceptJson()
            ->asForm()
            ->withBasicAuth($username, $password)
            ->withHeaders([
                'OCS-APIRequest' => 'true',
            ])
            ->post($baseUrl . '/ocs/v2.php/apps/files_sharing/api/v1/shares', $payload);

        if ($response->failed()) {
            throw new RuntimeException('Nextcloud share API request failed: ' . $response->body());
        }

        return $response->json() ?? [];
    }

    protected function webdavRequest(string $method, string $path, array $options = []): Response
    {
        $baseUrl = rtrim((string) $this->requireConfig('api_url'), '/');
        $username = (string) $this->requireConfig('username');
        $password = (string) $this->requireConfig('password');
        $timeout = (int) $this->additionalConfig('timeout', 30);

        $request = Http::timeout($timeout)
            ->withBasicAuth($username, $password)
            ->withHeaders($options['headers'] ?? []);

        $response = match (strtolower($method)) {
            'put' => $request->withBody((string) ($options['body'] ?? ''), $options['content_type'] ?? 'application/octet-stream')->put($baseUrl . $path),
            'get' => $request->get($baseUrl . $path),
            'delete' => $request->delete($baseUrl . $path),
            default => throw new RuntimeException("Unsupported Nextcloud WebDAV method [$method]."),
        };

        if ($response->failed()) {
            throw new RuntimeException('Nextcloud WebDAV request failed: ' . $response->body());
        }

        return $response;
    }

    protected function buildStoragePath(string $path): string
    {
        $username = (string) $this->requireConfig('username');
        $webdavBase = (string) $this->additionalConfig('webdav_base', '/remote.php/dav/files/{username}');
        $webdavBase = str_replace('{username}', $username, $webdavBase);

        $fullPath = $this->combinePath(
            (string) $this->documentConfig->root_folder,
            (string) $this->documentConfig->client_folder,
            $path
        );

        return '/' . trim($webdavBase, '/') . '/' . ltrim($fullPath, '/');
    }

    protected function sharePath(string $path): string
    {
        return '/' . ltrim($this->combinePath(
            (string) $this->documentConfig->root_folder,
            (string) $this->documentConfig->client_folder,
            $path
        ), '/');
    }

    protected function combinePath(string ...$segments): string
    {
        $clean = [];

        foreach ($segments as $segment) {
            $segment = trim($segment);
            if ($segment === '') {
                continue;
            }

            $clean[] = trim($segment, '/');
        }

        return implode('/', $clean);
    }
}
