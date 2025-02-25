<?php

namespace  App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::all();

        return ProjectResource::collection($projects);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $address = $request->input('address');
        $response = Http::get('http://localhost:300/geocode', [
            'address' => $address
        ]);

        if ($response->successful()) {
            $coords = $response->json();
        } else {
            $coords = ['lat' => null, 'lng' => null];
        }
        $project = Project::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'address' => $address,
            'latitude' => $coords['lat'] ?? null,
            'longitude' => $coords['lng'] ?? null
        ]);
        return new ProjectResource($project);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return new ProjectResource($project);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string'
        ]);
        $project->update($validated);

        return new ProjectResource($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(null, 204);
    }
}
