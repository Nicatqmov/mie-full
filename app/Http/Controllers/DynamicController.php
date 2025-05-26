<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DynamicModel;
class DynamicController extends Controller
{
    public function getProject($tableName)
    {
        $model = (new DynamicModel)->setTableName($tableName);
        return view('');
    }
    public function getEntity($tableName)
    {
        $model = (new DynamicModel)->setTableName($tableName);
        return response()->json($model->get());
    }

    public function storeEntity(Request $request, $tableName)
    {
        $model = (new DynamicModel)->setTableName($tableName);
        $model->fill($request->all());
        $model->save();
        return response()->json(['message' => 'Record saved successfully']);
    }
}
