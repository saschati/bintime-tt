<?php

declare(strict_types=1);

namespace App\Http\Response\Responder\Error;

use App\Http\Response\Responder;
use App\Models\EntityException;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: self::class,
    properties: [
        new OA\Property(
            property: 'errors',
            properties: [
                new OA\Property(property: 'code', type: 'integer'),
                new OA\Property(property: 'message', type: 'string'),
            ]
        ),
    ],
    type: 'object'
)]
class EntityResponder implements Responder
{
    /**
     * @param EntityException $data
     *
     * @return array[]
     */
    public function transform($data): array
    {
        return [
            'errors' => [
                'code' => $data->getCode(),
                'message' => $data->getMessage(),
            ],
        ];
    }
}
