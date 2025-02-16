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

        Assignment::create([
            'lead_id' => $request->input('lead_id'),
            'counselor_id' => $request->input('counselor_id')
        ]);

        return response()->json(['message' => 'Lead assigned successfully']);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'status' => 'required|in:New,In Progress,Bad Timing,Not Interested,Not Qualified'
        ]);

        $counselorId = auth()->user()->id;

        $assignment = Assignment::where('counselor_id', $counselorId)
            ->where('lead_id', $request->input('lead_id'))
            ->firstOrFail();

        $assignment->status = $request->input('status');
        $assignment->save();

        return response()->json([
            'message' => 'Assignment status updated successfully',
            'assignment' => $assignment
        ], 200);
    }
}
