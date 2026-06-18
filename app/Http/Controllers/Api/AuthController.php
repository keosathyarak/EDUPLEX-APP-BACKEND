<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
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
            'password' => Hash::make($request->password),
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
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'nullable|in:user,admin',
            'profile_picture' => 'nullable|image|max:2048'
        ]);

        $data = [
            'name' => $request->name ?? 'EduPlex User',
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user'
        ];

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('profiles', 'public');
        }

        $user = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user' => $user
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

        // Revoke all previous tokens for this user
        $user->tokens()->delete();
        
        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Create the cookie
        $cookie = cookie('api_token', $token, 1440, '/', null, false, false, false, 'Lax');

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user
        ])->withCookie($cookie);
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
            'users' => User::latest()->paginate(10)
        ]);
    }

    // ================= SHOW USER =================
    public function show(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'user' => User::findOrFail($id)
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
            'role' => 'nullable|in:user,admin',
            'profile_picture' => 'nullable|image|max:2048'
        ]);

        $data = $request->only(['name', 'email', 'role']);

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $data['profile_picture'] = $request->file('profile_picture')->store('profiles', 'public');
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

        $user = User::findOrFail($id);
        
        // Revoke all tokens before deleting user
        $user->tokens()->delete();
        
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User and all their active sessions deleted successfully'
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
        $user = $request->user();

        if ($user) {
            // Revoke all tokens to be sure
            $user->tokens()->delete();

            // Also logout from the web guard
            auth()->guard('web')->logout();
            
            // Invalidate session if it exists
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ])->withoutCookie(cookie()->forget('api_token'));
    }
}
