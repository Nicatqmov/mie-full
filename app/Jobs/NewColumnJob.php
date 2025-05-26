<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\NewColumnService;
use App\Models\Entity;

class NewColumnJob implements ShouldQueue
{
    use Queueable;
    protected $entityID;

    /**
     * Create a new job instance.
     */
    public function __construct($entityID)
    {
        $this->entityID = $entityID;
    }

    /**
     * Execute the job.
     */
    public function handle(NewColumnService $newColumnService): void
    {
        $entity = Entity::findorfail($this->entityID);
        $newColumnService->updateTableColumns($entity->id);
    }
}
