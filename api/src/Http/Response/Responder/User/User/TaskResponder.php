<?php

declare(strict_types=1);

namespace App\Http\Response\Responder\User\User;

use App\Http\Response\Responder;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: self::class,
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'title', type: 'string'),
        new OA\Property(property: 'description', type: 'string', format: 'email'),
        new OA\Property(property: 'status', type: 'string'),
        new OA\Property(property: 'startedAt', type: 'string', example: '2023-04-22 15:03'),
    ],
    type: 'object'
)]
class TaskResponder implements Responder
{
    /**
     * @param array $data
     *
     * @return array[]
     */
    public function transform($data): array
    {
        return $data;
    }
}
