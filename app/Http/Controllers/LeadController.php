<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('city', 'LIKE', "%$search%")
                  ->orWhere('phone', 'LIKE', "%$search%");
            });
        }

        $leads = $query->latest()->paginate(10);

        return response()->json($leads);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email',
            'phone' => 'required|string|max:15',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:10000',
            'status' => 'required|in:New,In Progress,Bad Timing,Not Interested,Not Qualified'
        ]);

        if ($validator->fails()) {
            Log::error('Validation Failed:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $lead = Lead::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'status' => $request->input('status'),
            ]);

            return response()->json([
                'message' => 'Lead created successfully',
                'lead' => $lead
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error storing lead: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $lead = Lead::findOrFail($id);
        return response()->json($lead);
    }

    public function update(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);


        // Define validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:leads,email,' . $lead->id,
            'phone' => 'sometimes|required|string|max:15',
            'city' => 'sometimes|required|string|max:100',
            'address' => 'sometimes|required|string|max:10000',
            'status' => 'required|in:New,In Progress,Bad Timing,Not Interested,Not Qualified'
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update the lead with the validated data
        $lead->update($validator->validated());

        // Return success response
        return response()->json([
            'message' => 'Lead updated successfully',
            'lead' => $lead
        ], 200);
    }

    public function destroy($id)
    {
        Lead::findOrFail($id)->delete();
        return response()->json(['message' => 'Lead deleted successfully']);
    }

    public function updateStatus(Request $request, $id)
        {
            $lead = Lead::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:New,In Progress,Bad Timing,Not Interested,Not Qualified' // Define allowed status values
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            $lead->status = $request->input('status');
            $lead->save();
            return response()->json([
                'message' => 'Lead status updated successfully',
                'lead' => $lead
            ], 200);
        }

}
