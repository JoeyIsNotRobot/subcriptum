<?php

namespace App\Policies;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApplicationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Application $application): bool
    {
        return $application->canBeAccessedBy($user);
    }

    /**
     * Determine whether the user can create models (apply to a project).
     */
    public function create(User $user): bool
    {
        return $user->isProfessional();
    }

    /**
     * Determine whether the user can apply to a specific project.
     */
    public function applyTo(User $user, Project $project): bool
    {
        // Deve ser um profissional
        if (!$user->isProfessional()) {
            return false;
        }

        // Projeto deve aceitar candidaturas
        if (!$project->allowsApplications()) {
            return false;
        }

        // Não pode já ter se candidatado
        if ($project->hasUserApplied($user)) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Application $application): bool
    {
        // Apenas o profissional dono pode atualizar
        if ($application->user_id !== $user->id) {
            return false;
        }

        // Apenas candidaturas pendentes podem ser atualizadas
        return $application->isPending();
    }

    /**
     * Determine whether the user can withdraw the application.
     */
    public function withdraw(User $user, Application $application): bool
    {
        // Apenas o profissional dono pode retirar
        if ($application->user_id !== $user->id) {
            return false;
        }

        // Não pode retirar se já foi aceita ou rejeitada
        return !$application->isFinal();
    }

    /**
     * Determine whether the user can accept the application.
     */
    public function accept(User $user, Application $application): bool
    {
        return $this->canManageApplication($user, $application)
            && $application->canTransitionTo(ApplicationStatus::Accepted);
    }

    /**
     * Determine whether the user can reject the application.
     */
    public function reject(User $user, Application $application): bool
    {
        return $this->canManageApplication($user, $application)
            && $application->canTransitionTo(ApplicationStatus::Rejected);
    }

    /**
     * Determine whether the user can mark application as under review.
     */
    public function review(User $user, Application $application): bool
    {
        return $this->canManageApplication($user, $application)
            && $application->canTransitionTo(ApplicationStatus::UnderReview);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Application $application): bool
    {
        // Apenas admins podem deletar
        return $user->isAdmin();
    }

    /**
     * Check if user can manage the application (accept/reject).
     */
    private function canManageApplication(User $user, Application $application): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->hasCompany()) {
            return false;
        }

        return $application->project->company_id === $user->company->id;
    }
}

