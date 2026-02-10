<?php

declare(strict_types=1);

namespace App\Actions\Applications;

use App\Enums\ApplicationStatus;
use App\Events\ApplicationRejected;
use App\Exceptions\InvalidApplicationStatusTransitionException;
use App\Models\Application;
use Illuminate\Support\Facades\DB;

class RejectApplicationAction
{
    /**
     * @throws InvalidApplicationStatusTransitionException
     */
    public function execute(Application $application, ?string $reason = null): Application
    {
        if (!$application->canTransitionTo(ApplicationStatus::Rejected)) {
            throw new InvalidApplicationStatusTransitionException(
                "Candidatura não pode ser rejeitada a partir do status '{$application->status->label()}'"
            );
        }

        return DB::transaction(function () use ($application, $reason) {
            $application->update([
                'status' => ApplicationStatus::Rejected,
                'rejected_at' => now(),
            ]);

            event(new ApplicationRejected($application, $reason));

            return $application->fresh();
        });
    }
}

