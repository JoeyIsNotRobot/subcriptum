<?php

namespace App\Policies;

use App\Models\ChatMessage;
use App\Models\Application;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatMessagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any messages.
     */
    public function viewAny(User $user, Application $application): bool
    {
        return $application->canBeAccessedBy($user) && $application->allowsChat();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ChatMessage $message): bool
    {
        return $message->canBeViewedBy($user);
    }

    /**
     * Determine whether the user can create messages.
     */
    public function create(User $user, Application $application): bool
    {
        // Deve ter acesso à candidatura
        if (!$application->canBeAccessedBy($user)) {
            return false;
        }

        // Chat deve estar habilitado
        return $application->allowsChat();
    }

    /**
     * Determine whether the user can update the message.
     */
    public function update(User $user, ChatMessage $message): bool
    {
        // Apenas o remetente pode editar
        if (!$message->isSentBy($user)) {
            return false;
        }

        // Apenas mensagens recentes (5 minutos)
        return $message->created_at->diffInMinutes(now()) <= 5;
    }

    /**
     * Determine whether the user can delete the message.
     */
    public function delete(User $user, ChatMessage $message): bool
    {
        // Apenas admins podem deletar
        return $user->isAdmin();
    }
}

