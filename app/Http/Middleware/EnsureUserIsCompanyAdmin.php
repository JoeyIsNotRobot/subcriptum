<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCompanyAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('filament.company.auth.login');
        }

        // Permitir admin do sistema ou admin de empresa
        if (!in_array($user->role, [UserRole::CompanyAdmin, UserRole::Admin])) {
            abort(403, 'Você não tem permissão para acessar este painel.');
        }

        return $next($request);
    }
}

