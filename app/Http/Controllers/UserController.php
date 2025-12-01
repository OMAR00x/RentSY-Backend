<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
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
                    'phone' => 'required|string|unique:users',
                    'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'password' => 'required|string|min:8|confirmed',
                    'role' => 'required|string',
                    'id_front' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'id_back' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'birthdate' => 'required|date',
                ],
                [
                    'phone.unique' => 'هذا الرقم مستخدم من قبل',
                    'required' => 'هذاالحقل مطلوب',
                    'date' => 'يجب ادخال تاريخ صحيح',
                ]
            );
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        }

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        if ($request->hasFile('id_front')) {
            $validated['id_front'] = $request->file('id_front')->store('ids', 'public');
        }
        if ($request->hasFile('id_back')) {
            $validated['id_back'] = $request->file('id_back')->store('ids', 'public');
        }

        $user = User::create($validated);
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token
        ], 'User registered successfully', 201);
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

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token
        ], 'User logged in successfully', 200);
    }
}
