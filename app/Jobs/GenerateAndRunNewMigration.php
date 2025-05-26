<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\NewMigrationService;
use App\Models\Project;


class GenerateAndRunNewMigration implements ShouldQueue
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
    public function handle(NewMigrationService $newMigrationService): void
    {
        $project = Project::findOrFail($this->projectId);
        $newMigrationService->generateAndRunNewMigration($this->projectId);
        $project->update(['is_new' => false]);
    }
    

}
