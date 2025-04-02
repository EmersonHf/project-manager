<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::with(['owner', 'manager'])->get();
        return response()->json($properties);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:house,apartment,condo,townhouse,land',
            'status' => 'required|in:available,rented,maintenance,sold',
            'listing_type' => 'required|in:sale,rent',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'square_feet' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'images' => 'nullable|array',
            'monthly_rent' => 'required_if:listing_type,rent|numeric|min:0',
            'deposit_amount' => 'required_if:listing_type,rent|numeric|min:0',
            'available_date' => 'required|date',
            'owner_id' => 'required|exists:users,id',
            'manager_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $property = Property::create($request->all());
        return response()->json($property->load(['owner', 'manager']), 201);
    }

    public function show(Property $property)
    {
        return response()->json($property->load(['owner', 'manager', 'leases', 'maintenanceRequests']));
    }

    public function update(Request $request, Property $property)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:100',
            'zip_code' => 'sometimes|string|max:20',
            'country' => 'sometimes|string|max:100',
            'price' => 'sometimes|numeric|min:0',
            'type' => 'sometimes|in:house,apartment,condo,townhouse,land',
            'status' => 'sometimes|in:available,rented,maintenance,sold',
            'listing_type' => 'sometimes|in:sale,rent',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'square_feet' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'images' => 'nullable|array',
            'monthly_rent' => 'required_if:listing_type,rent|numeric|min:0',
            'deposit_amount' => 'required_if:listing_type,rent|numeric|min:0',
            'available_date' => 'sometimes|date',
            'owner_id' => 'sometimes|exists:users,id',
            'manager_id' => 'sometimes|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $property->update($request->all());
        return response()->json($property->load(['owner', 'manager']));
    }

    public function destroy(Property $property)
    {
        $property->delete();
        return response()->json(null, 204);
    }
} 