<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaintenanceRequestController extends Controller
{
    public function index()
    {
        $requests = MaintenanceRequest::all();
        return response()->json($requests);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'scheduled_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $maintenanceRequest = MaintenanceRequest::create($request->all());
        return response()->json($maintenanceRequest, 201);
    }

    public function show(MaintenanceRequest $maintenanceRequest)
    {
        return response()->json($maintenanceRequest);
    }

    public function update(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'sometimes|exists:properties,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'priority' => 'sometimes|in:low,medium,high',
            'scheduled_date' => 'sometimes|date',
            'status' => 'sometimes|in:pending,in_progress,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $maintenanceRequest->update($request->all());
        return response()->json($maintenanceRequest);
    }

    public function destroy(MaintenanceRequest $maintenanceRequest)
    {
        $maintenanceRequest->delete();
        return response()->json(null, 204);
    }
} 