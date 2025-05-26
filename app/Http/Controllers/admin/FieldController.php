<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Fields;
use App\Models\Entity;
use App\Models\Project;
use App\Http\Requests\StoreFieldsRequest;
use App\Http\Requests\UpdateFieldsRequest;

class FieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project, Entity $entity)
    {
        $fields = $entity->fields;
        return view('admin.layouts.pages.fields.index',compact('project','entity','fields'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($field)
    {
        Fields::create([
            'entity_id' => $field['entity_id'],
            'name' => $field['name'],
            'column_name' => $field['column_name'],
            'type' => $field['type'],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Fields $fields)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fields $fields)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFieldsRequest $request, Fields $fields)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fields $fields)
    {
        //
    }
}
