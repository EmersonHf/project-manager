<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LeaseController extends Controller
{
    public function index()
    {
        $leases = Lease::all();
        return response()->json($leases);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'tenant_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_rent' => 'required|numeric',
            'security_deposit' => 'required|numeric',
            'pet_deposit' => 'required|numeric',
            'additional_fees' => 'required|array',
            'terms_and_conditions' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $lease = Lease::create($request->all());
        return response()->json($lease, 201);
    }

    public function show(Lease $lease)
    {
        return response()->json($lease);
    }

    public function update(Request $request, Lease $lease)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'sometimes|exists:properties,id',
            'tenant_id' => 'sometimes|exists:users,id',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'monthly_rent' => 'sometimes|numeric',
            'security_deposit' => 'sometimes|numeric',
            'pet_deposit' => 'sometimes|numeric',
            'additional_fees' => 'sometimes|array',
            'terms_and_conditions' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $lease->update($request->all());
        return response()->json($lease);
    }

    public function destroy(Lease $lease)
    {
        $lease->delete();
        return response()->json(null, 204);
    }
} 