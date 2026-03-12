<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ✅ تسجيل مستخدم جديد
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم إنشاء الحساب بنجاح',
            'token'   => $token,
            'user'    => $user,
        ], 201);
    }

    // ✅ تسجيل الدخول
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'البريد أو كلمة المرور غير صحيحة'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'token'   => $token,
            'user'    => $user,
        ]);
    }
 public function update(Request $request)
{
    $request->validate([
        'name'   => 'required|string|max:255',
        'avatar' => 'nullable|image|max:5120',
    ]);

    $updateData = ['name' => $request->name];

    if ($request->hasFile('avatar')) {
        $path = $request->file('avatar')->store('avatars', 'public');
        $updateData['avatar'] = $path;
    }

    $request->user()->update($updateData);

    return response()->json($request->user()->fresh());
}

    // ✅ تسجيل الخروج
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }

    // ✅ بيانات المستخدم الحالي
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}