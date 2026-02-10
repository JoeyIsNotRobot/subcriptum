<div class="space-y-4 p-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Valor Proposto</p>
            <p class="text-lg font-semibold">
                @if($application->proposed_value)
                    R$ {{ number_format($application->proposed_value, 2, ',', '.') }}
                @else
                    <span class="text-gray-400">Não informado</span>
                @endif
            </p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Prazo Estimado</p>
            <p class="text-lg font-semibold">
                @if($application->estimated_days)
                    {{ $application->estimated_days }} dias
                @else
                    <span class="text-gray-400">Não informado</span>
                @endif
            </p>
        </div>
    </div>

    <div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Mensagem da Proposta</p>
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $application->proposal_message }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 pt-4 border-t">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Profissional</p>
            <p class="font-medium">{{ $application->user->name }}</p>
            <p class="text-sm text-gray-500">{{ $application->user->email }}</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Avaliação</p>
            <p class="font-medium">
                @php
                    $rating = $application->user->averageRating();
                    $reviews = $application->user->totalReviews();
                @endphp
                @if($reviews > 0)
                    ⭐ {{ number_format($rating, 1) }} ({{ $reviews }} avaliações)
                @else
                    <span class="text-gray-400">Sem avaliações</span>
                @endif
            </p>
        </div>
    </div>

    @if($application->user->bio)
    <div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Sobre o Profissional</p>
        <p class="text-gray-700 dark:text-gray-300">{{ $application->user->bio }}</p>
    </div>
    @endif
</div>

