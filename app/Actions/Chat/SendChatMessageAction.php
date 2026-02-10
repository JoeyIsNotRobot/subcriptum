<?php

declare(strict_types=1);

namespace App\Actions\Chat;

use App\Events\ChatMessageSent;
use App\Exceptions\ChatNotAllowedException;
use App\Models\Application;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SendChatMessageAction
{
    /**
     * @throws ChatNotAllowedException
     */
    public function execute(User $sender, Application $application, string $message): ChatMessage
    {
        $this->validateChat($sender, $application);

        return DB::transaction(function () use ($sender, $application, $message) {
            $chatMessage = ChatMessage::create([
                'application_id' => $application->id,
                'sender_id' => $sender->id,
                'message' => $message,
            ]);

            event(new ChatMessageSent($chatMessage));

            return $chatMessage;
        });
    }

    /**
     * @throws ChatNotAllowedException
     */
    private function validateChat(User $sender, Application $application): void
    {
        if (!$application->canBeAccessedBy($sender)) {
            throw new ChatNotAllowedException(
                'Você não tem permissão para acessar este chat.'
            );
        }

        if (!$application->allowsChat()) {
            throw new ChatNotAllowedException(
                'Chat não está disponível para esta candidatura.'
            );
        }
    }
}

