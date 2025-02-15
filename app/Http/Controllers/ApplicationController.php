<?php 
namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function moveToApplication(Request $request) {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'counselor_id' => 'required|exists:users,id',
            'status' => 'required|in:In Progress,Approved,Rejected'
        ]);

        $application = Application::create($request->all());

        return response()->json($application, 201);
    }
}
