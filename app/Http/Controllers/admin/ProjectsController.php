<?php

namespace App\Http\Controllers\admin;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\DataTypeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Fields;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\admin\EntityController;
use App\Http\Controllers\admin\FieldController;
use App\Jobs\GenerateAndRunMigration;
use App\Jobs\DeleteProjectJob;
use Illuminate\Support\Facades\File;
use App\Jobs\GenerateAndRunNewMigration;
use App\Models\Entity;
use App\Jobs\NewColumnJob;

class ProjectsController extends Controller
{

    private $EntityController;
    private $FieldController;
    private $migrationService;
    public function __construct(EntityController $EntityController, FieldController $FieldController)
    {
        $this->EntityController = $EntityController;
        $this->FieldController = $FieldController;
    }

    public function index(){
        $projects = Auth::user()->projects()->where('status', 'active')->get();
        return view('admin.layouts.pages.projects.index',compact('projects'));
    }


    public function create(){
        $dataTypes = DataTypeModel::all();
        return view('admin.layouts.pages.projects.create',compact('dataTypes'));
    }

    public function edit($id){
        $project = Project::findorfail($id);
        $dataTypes = DataTypeModel::all();
        return view('admin.layouts.pages.projects.edit',compact('project','dataTypes'));
    }


    public function store(Request $request){
        $request->validate([
            'name' => 'required|max:255',
        ]);
        $usedTableNames = [];
        $token = Str::random(32).$request->name.Auth::id();
        $project = Project::create([
            'token' => $token,
            'name' => $request->name,
            'user_id' => Auth::id(),
            'status' => 'creating',
        ]);

        if ($project) {
            foreach ($request->entities as $entity) {
                $baseTableName = Str::plural($this->normalizeForSnake($entity['name']));
                $tableName = $baseTableName;
                
                $counter = 1;
                while (in_array($tableName, $usedTableNames)) {
                    $tableName = $baseTableName . '_' . $counter;
                    $counter++;
                }
                $usedTableNames[] = $tableName;
        
                $entity_array = [
                    'project_id' => $project->id, 
                    'name' => $entity['name'],
                    'table_name' => $tableName.'_'.$project->id,
                ];
        
                $entity_id = $this->EntityController->store($entity_array);
        
                $usedColumnNames = []; 
        
                foreach ($entity['fields'] as $field) {
                    $baseColumnName = $this->normalizeForSnake($field['name']);
                    $columnName = $baseColumnName;
        
                    $counter = 1;
                    while (in_array($columnName, $usedColumnNames)) {
                        $columnName = $baseColumnName . '_' . $counter;
                        $counter++;
                    }
                    $usedColumnNames[] = $columnName;
        
                    $field_array = [
                        'entity_id' => $entity_id,
                        'name' => $field['name'],
                        'column_name' => $columnName,
                        'type' => $field['type']
                    ];
        
                    $this->FieldController->store($field_array);
                }
            }

            
            GenerateAndRunMigration::dispatch($project->id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project creation has been queued. The system will now generate and run the necessary migrations. This process may take a few moments.',
                    'redirect' => route('projects.index')
                ]);
            }
            
            return redirect()->route('projects.index')
                ->with('info', 'Project creation has been queued. The system will now generate and run the necessary migrations. This process may take a few moments.');
        }
    }


    private function cleanupMigrationFiles($project)
    {
        $entities = $project->entities;
        foreach ($entities as $entity) {
            if ($entity->migration_file) {
                $migrationPath = database_path('projects_migrations/' . $entity->migration_file);
                if (File::exists($migrationPath)) {
                    File::delete($migrationPath);
                }
            }
        }
    }


    public function update(Request $request, $id){
        // dd($request->all());
        $project = Project::findorfail($id);
        $project->update([
            'name' => $request->project_name,
        ]);
        $usedTableNames = [];
        $requestedEntityIds = collect($request->entities)->pluck('id')->toArray();

        foreach ($project->entities as $entity) {
            if (!in_array($entity->id, $requestedEntityIds)) {
                $this->EntityController->destroy($entity->id);
            }
        }
        foreach ($request->entities as $entity) {
            if(!isset($entity['id'])){
                $baseTableName = Str::plural($this->normalizeForSnake($entity['name']));
                $tableName = $baseTableName;
                
                $counter = 1;
                while (in_array($tableName, $usedTableNames)) {
                    $tableName = $baseTableName . '_' . $counter;
                    $counter++;
                }
                $usedTableNames[] = $tableName;
        
                $entity_array = [
                    'project_id' => $project->id, 
                    'name' => $entity['name'],
                    'table_name' => $tableName.'_'.$project->id,
                ];
        
                $entity_id = $this->EntityController->store($entity_array);
        
                $usedColumnNames = []; 
        
                foreach ($entity['fields'] as $field) {
                    $baseColumnName = $this->normalizeForSnake($field['name']);
                    $columnName = $baseColumnName;
        
                    $counter = 1;
                    while (in_array($columnName, $usedColumnNames)) {
                        $columnName = $baseColumnName . '_' . $counter;
                        $counter++;
                    }
                    $usedColumnNames[] = $columnName;
        
                    $field_array = [
                        'entity_id' => $entity_id,
                        'name' => $field['name'],
                        'column_name' => $columnName,
                        'type' => $field['type']
                    ];
        
                    $this->FieldController->store($field_array);
    
                }
                \DB::commit();
                GenerateAndRunNewMigration::dispatch($project->id)->afterCommit();
            }else{
                $entityModel = Entity::findorfail($entity['id']);
                $usedColumnNames = [];
                $usedTableNames = [];
                $baseTableName = Str::plural($this->normalizeForSnake($entity['name']));
                $tableName = $baseTableName;
                
                $counter = 1;
                while (in_array($tableName, $usedTableNames)) {
                    $tableName = $baseTableName . '_' . $counter;
                    $counter++;
                }
                $usedTableNames[] = $tableName;
        
                $entity_array = [
                    'name' => $entity['name'],
                ];

                $this->EntityController->update($entity_array,$entityModel->id);
                $entityModel->fields()->delete();

                foreach ($entity['fields'] as $field) {
                    $baseColumnName = $this->normalizeForSnake($field['name']);
                    $columnName = $baseColumnName;
        
                    $counter = 1;
                    while (in_array($columnName, $usedColumnNames)) {
                        $columnName = $baseColumnName . '_' . $counter;
                        $counter++;
                    }
                    $usedColumnNames[] = $columnName;
        
                    $field_array = [
                        'entity_id' => $entityModel->id,
                        'name' => $field['name'],
                        'column_name' => $columnName,
                        'type' => $field['type']
                    ];
        
                    $this->FieldController->store($field_array);

                }
                \DB::commit();
                NewColumnJob::dispatch($entityModel->id);
            }
        }
        return redirect()->route('projects.index');
    }

    private function normalizeForSnake(string $value): string {
        return Str::snake(preg_replace('/\s+/', ' ', trim($value)));
    }

    public function destroy($id){
        $project = Project::findorfail($id);

        if($project && $project->status === 'active'){
            $project->update(['status' => 'deleting']);
            DeleteProjectJob::dispatch($project->id);
            return redirect()->route('projects.index')
                ->with('info', 'Project deletion has been queued. The system will now remove all associated tables and data. This process may take a few moments.');
        }
        
        return redirect()->route('projects.index')
            ->with('warning', 'Project is already being deleted or does not exist.');
    }
}
