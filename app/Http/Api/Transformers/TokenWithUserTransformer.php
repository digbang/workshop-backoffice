<?php

namespace App\Http\Api\Transformers;

use Flugg\Responder\Transformers\Transformer;
use WorkshopBackoffice\Entities\User;

class TokenWithUserTransformer extends Transformer
{
    public function transform(string $token, int $ttl, User $user): array
    {
        $token = (new TokenTransformer())->transform($token, $ttl);

        return $token + [
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstName' => $user->getName()->firstName(),
                'lastName' => $user->getName()->lastName(),
            ],
        ];
    }
}
