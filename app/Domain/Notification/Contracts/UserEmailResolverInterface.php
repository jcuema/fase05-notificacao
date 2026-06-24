<?php

namespace App\Domain\Notification\Contracts;

interface UserEmailResolverInterface
{
    /**
     * Resolve o endereço de e-mail de um usuário pelo seu ID.
     * Implementação padrão usa placeholder local; substituir por chamada
     * ao fase05-cadastro-usuario quando o serviço estiver disponível.
     */
    public function resolve(int $userId): string;
}
