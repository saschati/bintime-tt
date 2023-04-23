<?php

declare(strict_types=1);

namespace App\OpenApi\User;

use App\Http\Response\Responder\User\User\UserResponder;
use App\OpenApi\Paginator\PaginateResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: self::class,
    properties: [
        new OA\Property(
            property: 'data',
            ref: '#/components/schemas/' . UserResponder::class,
            type: 'object'
        ),
    ],
    type: 'object'
)]
class UserPaginateSchema extends PaginateResponse
{
}
