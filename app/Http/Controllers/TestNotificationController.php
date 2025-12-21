<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use App\Http\Traits\ResponseTrait;
use App\Services\NotificationService;

class TestNotificationController extends Controller
{
    use ResponseTrait;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Test Firebase initialization
     */
    public function testFirebase(Request $request)
    {
        try {
            $firebaseService = app(FirebaseService::class);

            if (!$firebaseService->isInitialized()) {
                return $this->errorResponse(
                    'Firebase غير مهيأ: ' . $firebaseService->getInitError(),
                    500
                );
            }

            return $this->successResponse([
                'initialized' => true,
                'credentials_path' => config('firebase.credentials'),
                'project_id' => config('firebase.project_id')
            ], 'Firebase تم تهيئته بنجاح ✓');
        } catch (\Exception $e) {
            return $this->errorResponse('خطأ في فحص Firebase: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Send test notification to current user
     */
    public function sendToMe(Request $request)
    {
        $user = $request->user();

        if (!$user->fcm_token) {
            return $this->errorResponse('لم يتم تعيين FCM Token لحسابك. يرجى تحديثه أولاً.', 400);
        }

        $result = $this->notificationService->sendToUser(
            $user->id,
            'إشعار تجريبي 🔔',
            'هذا إشعار تجريبي من تطبيق RentSY',
            ['type' => 'test', 'timestamp' => now()->toDateTimeString()]
        );

        if ($result['success']) {
            return $this->successResponse($result, 'تم إرسال الإشعار بنجاح ✓');
        }

        return $this->errorResponse('فشل إرسال الإشعار: ' . $result['error'], 500);
    }

    /**
     * Send test notification to specific user
     */
    public function sendToUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $result = $this->notificationService->sendToUser(
            $validated['user_id'],
            $validated['title'],
            $validated['body'],
            ['type' => 'custom', 'sent_by' => $request->user()->id]
        );

        if ($result['success']) {
            return $this->successResponse($result, 'تم إرسال الإشعار بنجاح ✓');
        }

        return $this->errorResponse('فشل إرسال الإشعار: ' . $result['error'], 500);
    }

    /**
     * Send test notification to all users
     */
    public function sendToAll(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $result = $this->notificationService->sendToAllUsers(
            $validated['title'],
            $validated['body'],
            ['type' => 'broadcast', 'sent_by' => $request->user()->id]
        );

        if ($result['success']) {
            return $this->successResponse($result, 'تم إرسال الإشعار لجميع المستخدمين بنجاح ✓');
        }

        return $this->errorResponse('فشل إرسال الإشعار: ' . $result['error'], 500);
    }

    /**
     * Get all users with FCM tokens
     */
    public function getUsersWithTokens()
    {
        $users = User::whereNotNull('fcm_token')
            ->select('id', 'first_name', 'last_name', 'phone', 'role')
            ->get();

        return $this->successResponse([
            'count' => $users->count(),
            'users' => $users
        ], 'تم جلب المستخدمين بنجاح');
    }

    /**
     * Check if user has FCM token
     */
    public function checkMyToken(Request $request)
    {
        $user = $request->user();

        return $this->successResponse([
            'has_token' => !empty($user->fcm_token),
            'token' => $user->fcm_token ? substr($user->fcm_token, 0, 20) . '...' : null
        ], 'تم فحص التوكن بنجاح');
    }

    /**
     * Test notification with custom data
     */
    public function sendCustom(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'body' => 'required|string',
            'data' => 'array',
        ]);

        $result = $this->notificationService->sendToUser(
            $validated['user_id'],
            $validated['title'],
            $validated['body'],
            $validated['data'] ?? []
        );

        if ($result['success']) {
            return $this->successResponse($result, 'تم إرسال الإشعار بنجاح ✓');
        }

        return $this->errorResponse('فشل إرسال الإشعار: ' . $result['error'], 500);
    }
}
