<?php

// app/Services/FirebaseService.php

namespace App\Services;

use Google_Client;
use GuzzleHttp\Client;

class FirebaseService
{
    public function getAccessToken()
    {
        $credentialsPath = storage_path('firebase/credentials.json');
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

        $client = new Google_Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope($scopes);

        $accessToken = $client->fetchAccessTokenWithAssertion();

        return $accessToken['access_token'] ?? null;
    }
// Add this method to your FirebaseService class
public function getProjectId()
{
    // Assuming the project ID is in the credentials file.
    $credentialsPath = storage_path('firebase/credentials.json');
    $credentials = json_decode(file_get_contents($credentialsPath), true);
    
    return $credentials['project_id'] ?? null;
}


    public function sendNotificationWithCustomData($deviceToken, $title, $body, $data)
{
    $accessToken = $this->getAccessToken();
    
    if (!$accessToken) {
        \Log::error('Failed to get access token.');
        return false;
    }

    $projectId = $this->getProjectId();
    if (!$projectId) {
        \Log::error('Project ID not found.');
        return false;
    }

    // Ensure your data payload is correctly formatted
    $message = [
        'message' => [
            'token' => $deviceToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'data' => $data, // Custom data payload
        ],
    ];

    // Log message for debugging
    \Log::info('Sending message:', $message);

    $client = new Client();

    try {
        $response = $client->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
            'json' => $message,
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ],
        ]);

        $responseBody = json_decode($response->getBody(), true);
        \Log::info('FCM Response: ', $responseBody);

        return $response->getStatusCode() === 200;
    } catch (\Exception $e) {
        \Log::error('Error sending FCM message: ' . $e->getMessage());
        return false;
    }
}

    
}
