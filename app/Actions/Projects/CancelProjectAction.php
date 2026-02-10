<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\Enums\ProjectStatus;
use App\Events\ProjectCancelled;
use App\Exceptions\InvalidProjectStatusTransitionException;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class CancelProjectAction
{
    /**
     * @throws InvalidProjectStatusTransitionException
     */
    public function execute(Project $project, ?string $reason = null): Project
    {
        if (!$project->canTransitionTo(ProjectStatus::Cancelled)) {
            throw new InvalidProjectStatusTransitionException(
                "Projeto não pode ser cancelado a partir do status '{$project->status->label()}'"
            );
        }

        return DB::transaction(function () use ($project, $reason) {
            $project->update([
                'status' => ProjectStatus::Cancelled,
            ]);

            event(new ProjectCancelled($project, $reason));

            return $project->fresh();
        });
    }
}

