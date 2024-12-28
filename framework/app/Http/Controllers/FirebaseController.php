<?php
// app/Http/Controllers/FirebaseController.php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

class  FirebaseController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Send an FCM message to a device.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendNotification()
    {
        $deviceToken = 'fH3w3WpxR-y2Dmey43weky:APA91bFhNBJfslYpIJbZK_qKLGWRrihWixlDwGRiE-SpwYDWnxgDKHShJiuw6iFttzIznhdnhljmbGauFOgqIKBvmHQkoGX5SvJnQ3y5jpk9jW6FTTWvpAo'; // Target device token
        $title = 'Test Notification'; 
        $body = 'This is a test FCM 2165151notification.';

        // $result = $this->firebaseService->sendNotification($deviceToken, $title, $body);
        $result = $this->firebaseService->sendNotificationWithCustomData($deviceToken, $title, $body, []);

        if ($result) {
            return response()->json(['message' => 'Notification sent successfully.'], 200);
        }

        return response()->json(['message' => 'Failed to send notification.'], 500);
    }
}
