<?php

declare(strict_types=1);

namespace App\Actions\Applications;

use App\Enums\ApplicationStatus;
use App\Events\ApplicationWithdrawn;
use App\Exceptions\InvalidApplicationStatusTransitionException;
use App\Models\Application;
use Illuminate\Support\Facades\DB;

class WithdrawApplicationAction
{
    /**
     * @throws InvalidApplicationStatusTransitionException
     */
    public function execute(Application $application): Application
    {
        if ($application->isFinal()) {
            throw new InvalidApplicationStatusTransitionException(
                'Candidatura não pode ser retirada pois já foi finalizada.'
            );
        }

        return DB::transaction(function () use ($application) {
            $application->update([
                'status' => ApplicationStatus::Withdrawn,
            ]);

            event(new ApplicationWithdrawn($application));

            return $application->fresh();
        });
    }
}

