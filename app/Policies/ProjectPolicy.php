<?php

namespace App\Policies;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
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
    public function view(User $user, Project $project): bool
    {
        // Projetos publicados são públicos
        if ($project->published_at !== null) {
            return true;
        }

        // Rascunhos só podem ser vistos pela empresa dona
        return $this->isProjectOwner($user, $project);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Apenas empresas podem criar projetos
        return $user->hasCompany();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        // Apenas a empresa dona pode editar
        if (!$this->isProjectOwner($user, $project)) {
            return false;
        }

        // Projetos finalizados não podem ser editados
        return !$project->isFinished();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        // Apenas a empresa dona pode deletar
        if (!$this->isProjectOwner($user, $project)) {
            return false;
        }

        // Apenas rascunhos podem ser deletados
        return $project->isDraft();
    }

    /**
     * Determine whether the user can publish the project.
     */
    public function publish(User $user, Project $project): bool
    {
        if (!$this->isProjectOwner($user, $project)) {
            return false;
        }

        return $project->isDraft();
    }

    /**
     * Determine whether the user can cancel the project.
     */
    public function cancel(User $user, Project $project): bool
    {
        if (!$this->isProjectOwner($user, $project)) {
            return false;
        }

        return $project->canTransitionTo(ProjectStatus::Cancelled);
    }

    /**
     * Determine whether the user can select a candidate.
     */
    public function selectCandidate(User $user, Project $project): bool
    {
        if (!$this->isProjectOwner($user, $project)) {
            return false;
        }

        return $project->isOpen() || $project->status === ProjectStatus::Selecting;
    }

    /**
     * Determine whether the user can complete the project.
     */
    public function complete(User $user, Project $project): bool
    {
        if (!$this->isProjectOwner($user, $project)) {
            return false;
        }

        return $project->isInProgress();
    }

    /**
     * Determine whether the user can view applications.
     */
    public function viewApplications(User $user, Project $project): bool
    {
        return $this->isProjectOwner($user, $project);
    }

    /**
     * Check if user owns the project through their company.
     */
    private function isProjectOwner(User $user, Project $project): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->hasCompany()) {
            return false;
        }

        return $project->company_id === $user->company->id;
    }
}

