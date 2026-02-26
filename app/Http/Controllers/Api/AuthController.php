<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    // ================= REGISTER (USER) =================
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name ?? 'EduPlex User',
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'user'
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    // ================= ADMIN REGISTER =================
    public function adminRegister(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'admin'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin created successfully',
            'admin' => $admin
        ]);
    }

    // ================= LOGIN =================
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid login'
            ], 401);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user
        ]);
    }

   // ================= UPDATE PROFILE PICTURE =================
public function updateProfilePicture(Request $request)
{
    $request->validate([
        'profile_picture' => 'required|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $user = $request->user();

    if ($user->profile_picture) {
        Storage::disk('public')->delete($user->profile_picture);
    }

    $path = $request->file('profile_picture')
        ->store('profiles', 'public');

    $user->update([
        'profile_picture' => $path
    ]);

    return response()->json([
        'success' => true,
        'profile_picture_url' => asset('storage/' . $path)
    ]);
}

    // ================= LIST USERS (ADMIN ONLY) =================
    public function index(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'users' => User::latest()->get()
        ]);
    }

    // ================= UPDATE USER =================
    public function update(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
            'role' => 'nullable|in:user,admin'
        ]);

        $data = $request->only(['name', 'email', 'role']);

        if ($request->password) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully'
        ]);
    }

    // ================= DELETE USER =================
    public function destroy(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        User::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
    // ================= UPDATE OWN PROFILE =================
public function updateProfile(Request $request)
{
    $user = $request->user();

    $request->validate([
        'name' => 'nullable|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|min:6|confirmed',
    ]);

    $data = $request->only(['name', 'email']);

    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    $user->update($data);

    return response()->json([
        'success' => true,
        'message' => 'Profile updated successfully',
        'user' => $user
    ]);
}

    
    // ================= LOGOUT =================
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
