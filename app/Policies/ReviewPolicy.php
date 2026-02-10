<?php

namespace App\Policies;

use App\Enums\ProjectStatus;
use App\Enums\ReviewType;
use App\Models\Project;
use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewPolicy
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
    public function view(User $user, Review $review): bool
    {
        // Avaliações públicas podem ser vistas por todos
        if ($review->is_public) {
            return true;
        }

        // Participantes podem ver suas avaliações
        return $review->reviewer_id === $user->id
            || $review->reviewee_id === $user->id
            || $user->isAdmin();
    }

    /**
     * Determine whether the user can create a review for a project.
     */
    public function create(User $user, Project $project): bool
    {
        // Projeto deve estar concluído
        if ($project->status !== ProjectStatus::Completed) {
            return false;
        }

        // Verificar se é a empresa ou o profissional do projeto
        $acceptedApplication = $project->acceptedApplication;

        if (!$acceptedApplication) {
            return false;
        }

        // É a empresa do projeto?
        if ($user->hasCompany() && $project->company_id === $user->company->id) {
            // Verificar se já avaliou
            return !$this->hasReviewed($user, $project, ReviewType::CompanyToProfessional);
        }

        // É o profissional selecionado?
        if ($acceptedApplication->user_id === $user->id) {
            // Verificar se já avaliou
            return !$this->hasReviewed($user, $project, ReviewType::ProfessionalToCompany);
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Review $review): bool
    {
        // Apenas o autor pode editar
        if ($review->reviewer_id !== $user->id) {
            return false;
        }

        // Apenas dentro de 24 horas
        return $review->created_at->diffInHours(now()) <= 24;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Review $review): bool
    {
        // Apenas admins podem deletar
        return $user->isAdmin();
    }

    /**
     * Check if user has already reviewed.
     */
    private function hasReviewed(User $user, Project $project, ReviewType $type): bool
    {
        return Review::query()
            ->where('project_id', $project->id)
            ->where('reviewer_id', $user->id)
            ->where('type', $type)
            ->exists();
    }
}

