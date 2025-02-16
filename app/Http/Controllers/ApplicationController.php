<?php 
namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
Use App\Models\Assignment;

class ApplicationController extends Controller
{
    /**
     * Move a lead to application.
     */
    public function moveToApplication(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'status' => 'sometimes|in:In Progress,Approved,Rejected'
        ]);

        $assignment = Assignment::where([
            'counselor_id' => auth()->user()->id,
            'lead_id' => $request->input('lead_id')
        ])->firstOrFail();

        $application = Application::create([
            'assignment_id' => $assignment->id
        ]);

        return response()->json($application, 201);
    }

    /**
     * Get all applications with lead and counselor details.
     */
    public function index()
    {
        $applications = Application::with(['lead', 'counselor'])->get();
        return response()->json($applications);
    }

    /**
     * Get a single application by ID.
     */
    public function show($id)
    {
        $application = Application::with(['lead', 'counselor'])->findOrFail($id);
        return response()->json($application);
    }

    /**
     * Update an application status.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:In Progress,Approved,Rejected'
        ]);

        $application = Application::findOrFail($id);
        $application->update($request->only('status'));

        return response()->json(['message' => 'Application updated successfully', 'application' => $application]);
    }

    /**
     * Delete an application.
     */
    public function destroy($id)
    {
        $application = Application::findOrFail($id);
        $application->delete();

        return response()->json(['message' => 'Application deleted successfully']);
    }
}
