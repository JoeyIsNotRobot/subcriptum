<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\Enums\ProjectStatus;
use App\Events\ProjectCompleted;
use App\Exceptions\InvalidProjectStatusTransitionException;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class CompleteProjectAction
{
    /**
     * @throws InvalidProjectStatusTransitionException
     */
    public function execute(Project $project): Project
    {
        if (!$project->canTransitionTo(ProjectStatus::Completed)) {
            throw new InvalidProjectStatusTransitionException(
                "Projeto não pode ser concluído a partir do status '{$project->status->label()}'"
            );
        }

        return DB::transaction(function () use ($project) {
            $project->update([
                'status' => ProjectStatus::Completed,
                'completed_at' => now(),
            ]);

            event(new ProjectCompleted($project));

            return $project->fresh();
        });
    }
}

