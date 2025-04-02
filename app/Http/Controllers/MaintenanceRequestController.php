<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaintenanceRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $maintenanceRequests = MaintenanceRequest::with(['property', 'reporter'])
            ->latest()
            ->paginate(10);

        return view('maintenance-requests.index', compact('maintenanceRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $properties = Property::all();
        return view('maintenance-requests.create', compact('properties'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'scheduled_date' => 'nullable|date',
            'attachments.*' => 'nullable|file|max:2048',
        ]);

        $validated['reported_by'] = auth()->id();
        $validated['status'] = 'pending';

        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $attachment) {
                $path = $attachment->store('maintenance', 'public');
                $attachments[] = $path;
            }
            $validated['attachments'] = $attachments;
        }

        $maintenanceRequest = MaintenanceRequest::create($validated);

        return redirect()->route('maintenance-requests.show', $maintenanceRequest)
            ->with('success', 'Maintenance request created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MaintenanceRequest $maintenanceRequest)
    {
        $maintenanceRequest->load(['property', 'reporter']);
        return view('maintenance-requests.show', compact('maintenanceRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaintenanceRequest $maintenanceRequest)
    {
        $properties = Property::all();
        return view('maintenance-requests.edit', compact('maintenanceRequest', 'properties'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'scheduled_date' => 'nullable|date',
            'completed_date' => 'nullable|date',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:2048',
        ]);

        if ($request->hasFile('attachments')) {
            // Delete old attachments
            if ($maintenanceRequest->attachments) {
                foreach ($maintenanceRequest->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment);
                }
            }

            $attachments = [];
            foreach ($request->file('attachments') as $attachment) {
                $path = $attachment->store('maintenance', 'public');
                $attachments[] = $path;
            }
            $validated['attachments'] = $attachments;
        }

        $maintenanceRequest->update($validated);

        return redirect()->route('maintenance-requests.show', $maintenanceRequest)
            ->with('success', 'Maintenance request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaintenanceRequest $maintenanceRequest)
    {
        // Delete attachments
        if ($maintenanceRequest->attachments) {
            foreach ($maintenanceRequest->attachments as $attachment) {
                Storage::disk('public')->delete($attachment);
            }
        }

        $maintenanceRequest->delete();

        return redirect()->route('maintenance-requests.index')
            ->with('success', 'Maintenance request deleted successfully.');
    }
}
