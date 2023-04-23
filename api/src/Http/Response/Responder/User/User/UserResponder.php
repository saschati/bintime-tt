<?php

declare(strict_types=1);

namespace App\Http\Response\Responder\User\User;

use App\Http\Response\Responder;
use OpenApi\Attributes as OA;
use yii\helpers\Json;

#[OA\Schema(
    schema: self::class,
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'username', type: 'string'),
        new OA\Property(property: 'email', type: 'string', format: 'email'),
        new OA\Property(property: 'name', properties: [
            new OA\Property(property: 'firstName', type: 'string'),
            new OA\Property(property: 'lastName', type: 'string'),
        ]),
        new OA\Property(property: 'registeredAt', type: 'string', example: '2023-04-22 15:03'),
    ],
    type: 'object'
)]
class UserResponder implements Responder
{
    /**
     * @param array $data
     *
     * @return array[]
     */
    public function transform($data): array
    {
        $fullName = Json::decode($data['name']);

        return [
            'id' => $data['id'],
            'username' => $data['username'],
            'email' => $data['email'],
            'name' => $fullName,
            'registeredAt' => $data['registeredAt'],
        ];
    }
}
