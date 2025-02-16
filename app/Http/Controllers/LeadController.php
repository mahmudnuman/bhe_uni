<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;

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
