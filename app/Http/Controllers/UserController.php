<?php 
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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

}
