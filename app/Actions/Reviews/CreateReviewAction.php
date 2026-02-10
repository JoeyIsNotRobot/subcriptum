<?php

declare(strict_types=1);

namespace App\Actions\Reviews;

use App\Enums\ProjectStatus;
use App\Enums\ReviewType;
use App\Events\ReviewCreated;
use App\Exceptions\ReviewNotAllowedException;
use App\Models\Project;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateReviewAction
{
    /**
     * @throws ReviewNotAllowedException
     */
    public function execute(User $reviewer, Project $project, array $data): Review
    {
        $this->validateReview($reviewer, $project);

        return DB::transaction(function () use ($reviewer, $project, $data) {
            $type = $this->determineReviewType($reviewer, $project);
            $reviewee = $this->determineReviewee($project, $type);

            $review = Review::create([
                'project_id' => $project->id,
                'company_id' => $project->company_id,
                'reviewer_id' => $reviewer->id,
                'reviewee_id' => $reviewee->id,
                'type' => $type,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
                'is_public' => $data['is_public'] ?? true,
            ]);

            event(new ReviewCreated($review));

            return $review;
        });
    }

    /**
     * @throws ReviewNotAllowedException
     */
    private function validateReview(User $reviewer, Project $project): void
    {
        if ($project->status !== ProjectStatus::Completed) {
            throw new ReviewNotAllowedException(
                'Avaliações só podem ser feitas após a conclusão do projeto.'
            );
        }

        $acceptedApplication = $project->acceptedApplication;

        if (!$acceptedApplication) {
            throw new ReviewNotAllowedException(
                'Projeto não possui candidatura aceita.'
            );
        }

        $isCompanyOwner = $reviewer->hasCompany() && $project->company_id === $reviewer->company->id;
        $isProfessional = $acceptedApplication->user_id === $reviewer->id;

        if (!$isCompanyOwner && !$isProfessional) {
            throw new ReviewNotAllowedException(
                'Apenas participantes do projeto podem fazer avaliações.'
            );
        }

        // Verificar se já avaliou
        $type = $isCompanyOwner
            ? ReviewType::CompanyToProfessional
            : ReviewType::ProfessionalToCompany;

        $existingReview = Review::query()
            ->where('project_id', $project->id)
            ->where('reviewer_id', $reviewer->id)
            ->where('type', $type)
            ->exists();

        if ($existingReview) {
            throw new ReviewNotAllowedException(
                'Você já avaliou este projeto.'
            );
        }
    }

    private function determineReviewType(User $reviewer, Project $project): ReviewType
    {
        if ($reviewer->hasCompany() && $project->company_id === $reviewer->company->id) {
            return ReviewType::CompanyToProfessional;
        }

        return ReviewType::ProfessionalToCompany;
    }

    private function determineReviewee(Project $project, ReviewType $type): User
    {
        if ($type === ReviewType::CompanyToProfessional) {
            return $project->acceptedApplication->user;
        }

        return $project->company->user;
    }
}

