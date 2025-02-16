<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Assignment;


class LeadController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user(); // Get the authenticated user
        $query = Lead::query();
    
        // If the user is not an admin, filter leads assigned to them
        if ($user->role !== 'admin') {
            $query->whereHas('assignment', function ($q) use ($user) {
                $q->where('counselor_id', $user->id);
            });
        }
    
        // Apply search filter if provided
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('city', 'LIKE', "%$search%")
                  ->orWhere('phone', 'LIKE', "%$search%");
            });
        }
    
        // Get leads with pagination
        $leads = $query->latest()->paginate(10);
    
        return response()->json($leads);
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email',
            'phone' => 'required|string|max:15',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:10000'
        ]);

        $lead = Lead::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'city' => $request->input('city'),
            'address' => $request->input('address')
        ]);

        return response()->json([
            'message' => 'Lead created successfully',
            'lead' => $lead
        ], 201);
    }

    public function show($id)
    {
        return Lead::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:leads,email,' . $lead->id,
            'phone' => 'sometimes|required|string|max:15',
            'city' => 'sometimes|required|string|max:100',
            'address' => 'sometimes|required|string|max:10000'
        ]);

        $lead->update([
            'name' => $request->input('name', $lead->name),
            'email' => $request->input('email', $lead->email),
            'phone' => $request->input('phone', $lead->phone),
            'city' => $request->input('city', $lead->city),
            'address' => $request->input('address', $lead->address)
        ]);

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
}
