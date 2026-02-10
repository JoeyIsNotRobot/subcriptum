<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\ChatMessage;
use App\Models\Company;
use App\Models\Project;
use App\Models\Review;
use App\Models\User;
use App\Policies\ApplicationPolicy;
use App\Policies\ChatMessagePolicy;
use App\Policies\CompanyPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\ReviewPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected array $policies = [
        Project::class => ProjectPolicy::class,
        Application::class => ApplicationPolicy::class,
        ChatMessage::class => ChatMessagePolicy::class,
        Review::class => ReviewPolicy::class,
        Company::class => CompanyPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->registerGates();
    }

    /**
     * Register the application's policies.
     */
    protected function registerPolicies(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    /**
     * Register custom gates.
     */
    protected function registerGates(): void
    {
        // Gate para verificar se o usuário pode gerenciar uma empresa
        Gate::define('manage-company', function (User $user) {
            return $user->hasCompany();
        });

        // Gate para verificar se o usuário pode se candidatar a projetos
        Gate::define('apply-to-projects', function (User $user) {
            return $user->isProfessional();
        });

        // Gate para verificar se é admin do sistema
        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });

        // Gate para acessar o painel da empresa
        Gate::define('access-company-panel', function (User $user) {
            return $user->isCompanyAdmin() || $user->isAdmin();
        });

        // Gate para acessar o painel admin
        Gate::define('access-admin-panel', function (User $user) {
            return $user->isAdmin();
        });
    }
}
