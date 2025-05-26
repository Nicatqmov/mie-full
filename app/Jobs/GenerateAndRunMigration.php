<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\MigrationService;
use App\Models\Project;

class GenerateAndRunMigration implements ShouldQueue
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
    public function handle(MigrationService $migrationService): void
    {
        try {
            $project = Project::findOrFail($this->projectId);
            
            if ($project->status === 'creating') {
                $migrationService->generateAndRunMigration($this->projectId);
                $project->update(['status' => 'active']);
                $project->update(['is_new' => false]);
            }
        } catch (\Exception $e) {
            $project->update(['status' => 'failed']);
            \Log::error("Migration job failed for project {$this->projectId}: " . $e->getMessage());
            throw $e;
        }
    }
}
