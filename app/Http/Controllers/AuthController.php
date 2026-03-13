<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

    // ✅ تحديث بيانات المستخدم (الاسم + الدولة الحالية)
    public function update(Request $request)
    {
        $request->validate([
            'name'                 => 'sometimes|string|max:255',
            'current_country_code' => 'nullable|string|max:10',
            'current_city'         => 'nullable|string|max:255',
        ]);

        $updateData = [];

        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }
        if ($request->has('current_country_code')) {
            $updateData['current_country_code'] = $request->current_country_code;
        }
        if ($request->has('current_city')) {
            $updateData['current_city'] = $request->current_city;
        }

        $request->user()->update($updateData);

        return response()->json($request->user()->fresh());
    }

    // ✅ رفع صورة الأفاتار
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:5120', // 5MB max
        ]);

        $user = $request->user();

        // حذف الصورة القديمة إن وجدت
        if ($user->avatar_url) {
            $oldPath = str_replace(url('/storage') . '/', '', $user->avatar_url);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        // حفظ الصورة الجديدة
        $path = $request->file('avatar')->store('avatars', 'public');
        $avatarUrl = url('/storage/' . $path);

        $user->update(['avatar_url' => $avatarUrl]);

        return response()->json([
            'message'    => 'تم تحديث الصورة بنجاح',
            'avatar_url' => $avatarUrl,
        ]);
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