<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Services\FirebaseService;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use App\Models\Notification as ModelsNotification;

class NotificationService
{
    protected FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Send notification to a single device
     */
    public function sendToDevice(string $fcmToken, string $title, string $body, array $data = []): array
    {
        if (!$this->firebaseService->isInitialized()) {
            return [
                'success' => false,
                'error' => $this->firebaseService->getInitError()
            ];
        }

        try {
            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification(Notification::create($title, $body))
                ->withData($data)
                ->withAndroidConfig($this->getAndroidConfig())
                ->withApnsConfig($this->getApnsConfig());

            $result = $this->firebaseService->getMessaging()->send($message);



            return [
                'success' => true,
                'message_id' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send notification to multiple devices
     */
    public function sendToMultipleDevices(array $fcmTokens, string $title, string $body, array $data = []): array
    {
        if (!$this->firebaseService->isInitialized()) {
            return [
                'success' => false,
                'error' => $this->firebaseService->getInitError()
            ];
        }

        if (empty($fcmTokens)) {
            return [
                'success' => false,
                'error' => 'No FCM tokens provided'
            ];
        }

        try {
            $message = CloudMessage::new()
                ->withNotification(Notification::create($title, $body))
                ->withData($data)
                ->withAndroidConfig($this->getAndroidConfig())
                ->withApnsConfig($this->getApnsConfig());

            $report = $this->firebaseService->getMessaging()->sendMulticast($message, $fcmTokens);

            return [
                'success' => true,
                'success_count' => $report->successes()->count(),
                'failure_count' => $report->failures()->count(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send notification to a topic
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): array
    {
        if (!$this->firebaseService->isInitialized()) {
            return [
                'success' => false,
                'error' => $this->firebaseService->getInitError()
            ];
        }

        try {
            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification(Notification::create($title, $body))
                ->withData($data)
                ->withAndroidConfig($this->getAndroidConfig())
                ->withApnsConfig($this->getApnsConfig());

            $result = $this->firebaseService->getMessaging()->send($message);

            return [
                'success' => true,
                'message_id' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send notification to a user by user ID
     */
    public function sendToUser(int $userId, string $title, string $body, array $data = []): array
    {
        $user = User::find($userId);

        if (!$user) {
            return [
                'success' => false,
                'error' => 'User not found'
            ];
        }

        if (!$user->fcm_token) {
            return [
                'success' => false,
                'error' => 'User has no FCM token'
            ];
        }

        $storeNotifications = ModelsNotification::create([
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);
        return $this->sendToDevice($user->fcm_token, $title, $body, $data);
    }

    /**
     * Send notification to all users
     */
    public function sendToAllUsers(string $title, string $body, array $data = []): array
    {
        $tokens = User::whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();

        if (empty($tokens)) {
            return [
                'success' => false,
                'error' => 'No users with FCM tokens found'
            ];
        }

        return $this->sendToMultipleDevices($tokens, $title, $body, $data);
    }

    /**
     * Subscribe a token to a topic
     */
    public function subscribeToTopic(string $fcmToken, string $topic): array
    {
        if (!$this->firebaseService->isInitialized()) {
            return [
                'success' => false,
                'error' => $this->firebaseService->getInitError()
            ];
        }

        try {
            $this->firebaseService->getMessaging()->subscribeToTopic($topic, [$fcmToken]);

            return [
                'success' => true,
                'message' => "Subscribed to topic: {$topic}"
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Unsubscribe a token from a topic
     */
    public function unsubscribeFromTopic(string $fcmToken, string $topic): array
    {
        if (!$this->firebaseService->isInitialized()) {
            return [
                'success' => false,
                'error' => $this->firebaseService->getInitError()
            ];
        }

        try {
            $this->firebaseService->getMessaging()->unsubscribeFromTopic($topic, [$fcmToken]);

            return [
                'success' => true,
                'message' => "Unsubscribed from topic: {$topic}"
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get Android specific configuration
     */
    protected function getAndroidConfig(): AndroidConfig
    {
        return AndroidConfig::fromArray([
            'priority' => 'high',
            'notification' => [
                'sound' => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ]);
    }

    /**
     * Get iOS specific configuration
     */
    protected function getApnsConfig(): ApnsConfig
    {
        return ApnsConfig::fromArray([
            'payload' => [
                'aps' => [
                    'sound' => 'default',
                    'badge' => 1,
                ],
            ],
        ]);
    }
}
