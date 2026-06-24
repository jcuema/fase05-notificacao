<?php

namespace App\Infrastructure\UserResolver;

use App\Domain\Notification\Contracts\UserEmailResolverInterface;

class PlaceholderUserEmailResolver implements UserEmailResolverInterface
{
    /**
     * Retorna um e-mail placeholder baseado no user_id.
     * TODO: substituir por chamada HTTP ao fase05-cadastro-usuario quando disponível.
     */
    public function resolve(int $userId): string
    {
        return "user-{$userId}@fase05.local";
    }
}
