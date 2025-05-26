<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\Project;
use App\Http\Requests\StoreEntityRequest;
use App\Http\Requests\UpdateEntityRequest;

class EntityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        $entities = $project->entities->all();
        return view('admin.layouts.pages.entities.index',compact('entities','project'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($entity)
    {
        $entity_id = Entity::create([
            'project_id' =>$entity['project_id'],
            'name' =>$entity['name'],
            'table_name' =>$entity['table_name'],
        ]);

        return $entity_id->id;
    }

    /**
     * Display the specified resource.
     */
    public function show(Entity $entity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entity $entity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($entity_array, $id)
    {
        $entity = Entity::findorfail($id);
        $entity->update([
            'name' => $entity_array['name'],
        ]);
        return $entity->id;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $entity = Entity::findorfail($id);
            $entity->delete();
            return redirect()->back()->with('success', 'Entity deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete entity');
        }
    }
}
