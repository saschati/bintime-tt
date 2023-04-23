<?php

declare(strict_types=1);

namespace App\OpenApi\User;

use App\Http\Response\Responder\User\User\TaskResponder;
use App\OpenApi\Paginator\PaginateResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: self::class,
    properties: [
        new OA\Property(
            property: 'data',
            ref: '#/components/schemas/' . TaskResponder::class,
            type: 'object'
        ),
    ],
    type: 'object'
)]
class TaskPaginateSchema extends PaginateResponse
{
}
