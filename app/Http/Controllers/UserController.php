<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\ResponseTrait;

class UserController extends Controller
{
    use ResponseTrait;

    public function register(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'phone' => 'required|unique:users|numeric',
                    'password' => 'required|string|min:8|confirmed',
                    'role' => 'required|string|in:renter,owner',
                    'avatar' => 'image|mimes:jpeg,png,jpg|max:4048',
                    'id_front' => 'image|mimes:jpeg,png,jpg|max:4048',
                    'id_back' => 'image|mimes:jpeg,png,jpg|max:4048',
                    'birthdate' => 'required|date',
                ],
                [
                    'phone.unique' => 'هذا الرقم مستخدم من قبل',
                    'required' => 'هذاالحقل مطلوب',
                    'date' => 'يجب ادخال تاريخ صحيح',
                    'numeric' => 'يجب ادخال رقم صحيح',
                    'password.confirmed' => 'كلمة السر غير متطابقة',
                    'in' => 'القيمة المدخلة غير صحيحة',
                ]
            );
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        }

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        if ($request->hasFile('avatar')) {
            $user->images()->create([
                'url' => $request->file('avatar')->store('avatars', 'public'),
                'type' => 'avatar'
            ]);
        }
        if ($request->hasFile('id_front')) {
            $user->images()->create([
                'url' => $request->file('id_front')->store('ids', 'local'),
                'type' => 'id_front'
            ]);
        }
        if ($request->hasFile('id_back')) {
            $user->images()->create([
                'url' => $request->file('id_back')->store('ids', 'local'),
                'type' => 'id_back'
            ]);
        }

        return $this->successResponse([
            'user' => $user->load('avatar', 'idFront', 'idBack'),
        ], 'تم إنشاء الحساب بنجاح، حسابك قيد المراجعة وسيتم إشعارك عند الموافقة', 201);
    }

    public function login(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'phone' => 'required|string',
                    'password' => 'required|string'
                ],
                [
                    'required' => 'هذاالحقل مطلوب',
                ]
            );
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        }

        $user = User::where('phone', $validated['phone'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return $this->errorResponse('الرقم أو كلمة السر غير صحيحة', 401);
        }

        if ($user->status === 'pending') {
            return $this->errorResponse('حسابك قيد المراجعة، يرجى الانتظار حتى يتم الموافقة عليه', 403);
        }


        if ($user->status === 'rejected') {
            return $this->errorResponse('تم رفض حسابك', 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user->load('avatar'),
            'token' => $token
        ], 'تم تسجيل الدخول بنجاح', 200);
    }

    public function profile(Request $request)
    {
        $user = $request->user()->load('avatar');

        return response()->json($user);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|max:4048'
        ]);

        $user = $request->user();

        if (isset($validated['first_name'])) {
            $user->first_name = $validated['first_name'];
        }
        if (isset($validated['last_name'])) {
            $user->last_name = $validated['last_name'];
        }
        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($request->hasFile('avatar')) {
            if ($oldAvatar = $user->avatar) {
                Storage::disk('public')->delete($oldAvatar->url);
                $oldAvatar->delete();
            }
            $user->images()->create([
                'url' => $request->file('avatar')->store('avatars', 'public'),
                'type' => 'avatar'
            ]);
        }

        return $this->successResponse(
            $user->load('avatar'),
            'تم تحديث البروفايل بنجاح'
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'تم تسجيل الخروج بنجاح');
    }
}
