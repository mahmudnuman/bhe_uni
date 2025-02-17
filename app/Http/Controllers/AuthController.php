<?php 
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function index() {
        $users = User::all();
        return response()->json($users);
    }
    public function show($id) {
        $user = User::findOrFail($id);
        return response()->json($user);
    }
    
    public function destroy($id) {
        $user = User::findOrFail($id);
        $user->delete();
    
        return response()->json(['message' => 'User deleted successfully']);
    }
    
    public function update(Request $request, $id) {
        $user = User::findOrFail($id);
    
        $request->validate([
            'name' => 'sometimes|required',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|min:6',
            'role' => 'sometimes|required|in:admin,counselor'
        ]);
    
        // Update only the validated fields
        $user->update($request->only(['name', 'email', 'password', 'role']));
    
        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }
    
    public function register(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,counselor'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);



        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function login(Request $request) {
        $credentials = $request->only('email', 'password');
    
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        $user = auth()->user();
    
        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }
    

    public function logout() {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Logged out successfully']);
    }
}
