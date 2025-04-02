<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leases = Lease::with(['property', 'tenant'])
            ->latest()
            ->paginate(10);

        return view('leases.index', compact('leases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $properties = Property::where('status', 'available')->get();
        $tenants = User::role('tenant')->get();
        return view('leases.create', compact('properties', 'tenants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'tenant_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'security_deposit' => 'required|numeric|min:0',
            'pet_deposit' => 'nullable|numeric|min:0',
            'additional_fees' => 'nullable|array',
            'terms_and_conditions' => 'nullable|string',
            'documents.*' => 'nullable|file|max:2048',
        ]);

        if ($request->hasFile('documents')) {
            $documents = [];
            foreach ($request->file('documents') as $document) {
                $path = $document->store('leases', 'public');
                $documents[] = $path;
            }
            $validated['documents'] = $documents;
        }

        $lease = Lease::create($validated);

        // Update property status
        $lease->property->update(['status' => 'rented']);

        return redirect()->route('leases.show', $lease)
            ->with('success', 'Lease created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lease $lease)
    {
        $lease->load(['property', 'tenant', 'payments', 'maintenanceRequests']);
        return view('leases.show', compact('lease'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lease $lease)
    {
        $properties = Property::where('status', 'available')
            ->orWhere('id', $lease->property_id)
            ->get();
        $tenants = User::role('tenant')->get();
        return view('leases.edit', compact('lease', 'properties', 'tenants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lease $lease)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'tenant_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'security_deposit' => 'required|numeric|min:0',
            'pet_deposit' => 'nullable|numeric|min:0',
            'additional_fees' => 'nullable|array',
            'status' => 'required|in:active,expired,terminated,pending',
            'terms_and_conditions' => 'nullable|string',
            'documents.*' => 'nullable|file|max:2048',
        ]);

        if ($request->hasFile('documents')) {
            // Delete old documents
            if ($lease->documents) {
                foreach ($lease->documents as $document) {
                    Storage::disk('public')->delete($document);
                }
            }

            $documents = [];
            foreach ($request->file('documents') as $document) {
                $path = $document->store('leases', 'public');
                $documents[] = $path;
            }
            $validated['documents'] = $documents;
        }

        $lease->update($validated);

        // Update property status based on lease status
        $lease->property->update([
            'status' => $validated['status'] === 'active' ? 'rented' : 'available'
        ]);

        return redirect()->route('leases.show', $lease)
            ->with('success', 'Lease updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lease $lease)
    {
        // Delete lease documents
        if ($lease->documents) {
            foreach ($lease->documents as $document) {
                Storage::disk('public')->delete($document);
            }
        }

        // Update property status
        $lease->property->update(['status' => 'available']);

        $lease->delete();

        return redirect()->route('leases.index')
            ->with('success', 'Lease deleted successfully.');
    }
}
