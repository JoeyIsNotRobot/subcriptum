<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\Enums\ProjectStatus;
use App\Events\ProjectPublished;
use App\Exceptions\InvalidProjectStatusTransitionException;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class PublishProjectAction
{
    /**
     * @throws InvalidProjectStatusTransitionException
     */
    public function execute(Project $project): Project
    {
        if (!$project->canTransitionTo(ProjectStatus::Open)) {
            throw new InvalidProjectStatusTransitionException(
                "Projeto não pode ser publicado a partir do status '{$project->status->label()}'"
            );
        }

        return DB::transaction(function () use ($project) {
            $project->update([
                'status' => ProjectStatus::Open,
                'published_at' => now(),
            ]);

            event(new ProjectPublished($project));

            return $project->fresh();
        });
    }
}

