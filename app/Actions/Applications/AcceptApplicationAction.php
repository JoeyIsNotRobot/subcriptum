<?php

declare(strict_types=1);

namespace App\Actions\Applications;

use App\Enums\ApplicationStatus;
use App\Enums\ProjectStatus;
use App\Events\ApplicationAccepted;
use App\Exceptions\InvalidApplicationStatusTransitionException;
use App\Models\Application;
use Illuminate\Support\Facades\DB;

class AcceptApplicationAction
{
    /**
     * @throws InvalidApplicationStatusTransitionException
     */
    public function execute(Application $application): Application
    {
        if (!$application->canTransitionTo(ApplicationStatus::Accepted)) {
            throw new InvalidApplicationStatusTransitionException(
                "Candidatura não pode ser aceita a partir do status '{$application->status->label()}'"
            );
        }

        return DB::transaction(function () use ($application) {
            // Aceitar esta candidatura
            $application->update([
                'status' => ApplicationStatus::Accepted,
                'accepted_at' => now(),
            ]);

            // Rejeitar todas as outras candidaturas do projeto
            Application::query()
                ->where('project_id', $application->project_id)
                ->where('id', '!=', $application->id)
                ->whereNotIn('status', [
                    ApplicationStatus::Rejected,
                    ApplicationStatus::Withdrawn,
                ])
                ->update([
                    'status' => ApplicationStatus::Rejected,
                    'rejected_at' => now(),
                ]);

            // Atualizar status do projeto para "em andamento"
            $application->project->update([
                'status' => ProjectStatus::InProgress,
                'started_at' => now(),
            ]);

            event(new ApplicationAccepted($application));

            return $application->fresh();
        });
    }
}

