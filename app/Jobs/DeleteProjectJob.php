<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Project;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class DeleteProjectJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $projectId;

    /**
     * Create a new job instance.
     */
    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $project = Project::find($this->projectId);

        if ($project) {
            try {
                $project->update(['status' => 'deleting']);

                $entities = $project->entities;
                
                foreach($entities as $entity) {
                    $tableName = $entity->table_name . '_' . $project->id;
                    
                    if(Schema::connection('mie_projects')->hasTable($tableName)) {
                        Schema::connection('mie_projects')->drop($tableName);
                    }

                    if ($entity->migration_file) {
                        $migrationPath = database_path('projects_migrations/' . $entity->migration_file);
                        if (File::exists($migrationPath)) {
                            File::delete($migrationPath);
                        }
                    }
                }
                $project->delete();
            } catch (\Exception $e) {
                $project->update(['status' => 'active']);
                throw $e;
            }
        }
    }
} 