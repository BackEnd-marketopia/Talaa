<?php

namespace App\Services\Implementations;

use App\Repository\Contracts\NotificationRepositoryInterface;
use App\Services\Contracts\FirebaseServiceInterface;
use Google\Client as GoogleClient;
use Google\Service\FirebaseCloudMessaging;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService implements FirebaseServiceInterface
{
    protected $notificationRepo;
    public function __construct(NotificationRepositoryInterface $notificationRepo)
    {
        $this->notificationRepo = $notificationRepo;
    }
    public function getFirebaseAccessToken()
    {
        $keyFilePath = $this->getFirebaseCredentialsPath();

        if (!File::exists($keyFilePath)) {
            Log::error('Firebase credentials file was not found.', [
                'path' => $keyFilePath,
            ]);

            return null;
        }

        $credentials = json_decode(File::get($keyFilePath), true);

        if (!is_array($credentials) || ! $this->hasValidServiceAccountCredentials($credentials)) {
            Log::error('Firebase credentials file is invalid. Please upload a Firebase service account JSON file.', [
                'path' => $keyFilePath,
            ]);

            return null;
        }

        try {
            $googleClient = new GoogleClient();
            $googleClient->setAuthConfig($credentials);
            $googleClient->addScope(FirebaseCloudMessaging::CLOUD_PLATFORM);
            $googleClient->fetchAccessTokenWithAssertion();

            return $googleClient->getAccessToken()['access_token'] ?? null;
        } catch (\Throwable $exception) {
            Log::error('Failed to get Firebase access token.', [
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function hasValidServiceAccountCredentials(array $credentials): bool
    {
        foreach (['type', 'project_id', 'private_key', 'client_email', 'token_uri'] as $key) {
            if (empty($credentials[$key])) {
                return false;
            }
        }

        return $credentials['type'] === 'service_account';
    }

    private function getFirebaseCredentialsPath(): string
    {
        $configuredPath = config('services.firebase.credentials');

        if (!empty($configuredPath)) {
            return $configuredPath;
        }

        $defaultPath = storage_path('firebase/credentials.json');
        $legacyPath = storage_path('firebase/credintials.json');

        return File::exists($defaultPath) ? $defaultPath : $legacyPath;
    }

    public function sendNotification($title, $body, $type, $to, $save = false, $user_id = null, $image = null, $data = null)
    {
        if ($save == true) {
            $this->notificationRepo->create([
                'title' => $title,
                'body' => $body,
                'type' => $type,
                'to' => $to,
                'user_id' => $user_id,
                'image' => $image,
                'data' => $data,
                'is_read' => false,
            ]);
        }
        $accessToken = self::getFirebaseAccessToken();
        if (!$accessToken) {
            return [
                'success' => false,
                'message' => __('message.Failed to get Firebase Access Token')
            ];
        }

        $client = new HttpClient();
        $url = "https://fcm.googleapis.com/v1/projects/talaa-2bd5f/messages:send";

        $topicOrToken = $type == 'topic' ? 'topic' : 'token';
        $payload = [
            "message" => [
                $topicOrToken => $to,
                "notification" => [
                    "title" => $title,
                    "body" => $body,
                    "image" => env('APP_URL') . '/storage/' . $image ?? "",
                ],
                "data" => $data,
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer $accessToken",
            'Content-Type' => 'application/json',
        ])->post($url, $payload);
        Log::info(json_decode($response->getBody(), true));
        return [
            'success' => true,
            'message' => __('message.Notification sent successfully'),
            'data'    => json_decode($response->getBody(), true)
        ];
    }
}
