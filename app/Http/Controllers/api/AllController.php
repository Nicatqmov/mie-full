<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Entity;
use App\Models\Field;
use App\Http\Resources\ApiResource;
use App\Models\User;
use App\Http\Resources\ProjectTableResource;
use App\Http\Resources\ProjectTableDataResource;
class AllController extends Controller
{
    public function getAllProjects(Request $request)
    {
        $response = $request->user()->projects()->get();
        return ApiResource::collection($response);
    }

    public function getProject(Request $request)
    {
        $project_token = $request->header('X-Project-Token');
        $response = $request->user()->projects()->where('token', $project_token)->first();
        return new ApiResource($response);
    }

    public function getProjectTables(Request $request)
    {
        $project_token = $request->header('X-Project-Token');
        $project = $request->user()->projects()->where('token', $project_token)->first();
        $tables = Entity::where('project_id', $project->id)->get();
        return ProjectTableResource::collection($tables);
    }

    public function getProjectTableData(Request $request)
    {
        $project_token = $request->header('X-Project-Token');
        $project = $request->user()->projects()->where('token', $project_token)->first();
        $tableName = $request->input('table_name');
        $table = Entity::where('project_id', $project->id)->where('table_name', $tableName)->first();
        if(!$table){
            return response()->json(['message' => 'Table not found'], 404);
        }
        $dynamicModel = new \App\Models\DynamicModel();
        $data = $dynamicModel->setTableName($table->table_name.'_'.$project->id)->get();
        return response()->json($data);
    }

    }
