<?php 
namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Lead;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function assignLead(Request $request) {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'counselor_id' => 'required|exists:users,id'
        ]);

        Assignment::create($request->all());

        return response()->json(['message' => 'Lead assigned successfully']);
    }
}
