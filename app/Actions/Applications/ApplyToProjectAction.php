<?php

declare(strict_types=1);

namespace App\Actions\Applications;

use App\Enums\ApplicationStatus;
use App\Events\ApplicationSubmitted;
use App\Exceptions\ApplicationNotAllowedException;
use App\Models\Application;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ApplyToProjectAction
{
    /**
     * @throws ApplicationNotAllowedException
     */
    public function execute(User $user, Project $project, array $data): Application
    {
        $this->validateApplication($user, $project);

        return DB::transaction(function () use ($user, $project, $data) {
            $application = Application::create([
                'user_id' => $user->id,
                'project_id' => $project->id,
                'status' => ApplicationStatus::Pending,
                'proposal_message' => $data['proposal_message'],
                'proposed_value' => $data['proposed_value'] ?? null,
                'estimated_days' => $data['estimated_days'] ?? null,
                'applied_at' => now(),
            ]);

            event(new ApplicationSubmitted($application));

            return $application;
        });
    }

    /**
     * @throws ApplicationNotAllowedException
     */
    private function validateApplication(User $user, Project $project): void
    {
        if (!$user->isProfessional()) {
            throw new ApplicationNotAllowedException(
                'Apenas profissionais podem se candidatar a projetos.'
            );
        }

        if (!$project->allowsApplications()) {
            throw new ApplicationNotAllowedException(
                'Este projeto não está aceitando candidaturas.'
            );
        }

        if ($project->hasUserApplied($user)) {
            throw new ApplicationNotAllowedException(
                'Você já se candidatou a este projeto.'
            );
        }
    }
}

