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
                    'password' => 'required|string|min:8|confirmed',
                    'role' => 'required|string',
                    'avatar' => 'nullable|image|max:2048',
                    'id_front' => 'nullable|image|max:2048',
                    'id_back' => 'nullable|image|max:2048',
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

        $user = User::create($validated);

        if ($request->hasFile('avatar')) {
            $user->images()->create([
                'url' => $request->file('avatar')->store('avatars', 'public'),
                'type' => 'avatar'
            ]);
        }
        if ($request->hasFile('id_front')) {
            $user->images()->create([
                'url' => $request->file('id_front')->store('ids', 'public'),
                'type' => 'id_front'
            ]);
        }
        if ($request->hasFile('id_back')) {
            $user->images()->create([
                'url' => $request->file('id_back')->store('ids', 'public'),
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
        /*
        if ($user->status === 'pending') {
            return $this->errorResponse('حسابك قيد المراجعة، يرجى الانتظار حتى يتم الموافقة عليه', 403);
        }
*/

        /*
        if ($user->status === 'rejected') {
            return $this->errorResponse('تم رفض حسابك', 403);
        }
*/
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user->load('avatar'),
            'token' => $token
        ], 'User logged in successfully', 200);
    }
}
