<?php

declare(strict_types=1);

namespace App\OpenApi\Paginator;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Pagination',
    properties: [
        new OA\Property(property: 'meta', properties: [
            new OA\Property(property: 'currentPage', type: 'integer'),
            new OA\Property(property: 'perPage', type: 'integer'),
            new OA\Property(property: 'totalCount', type: 'integer'),
            new OA\Property(property: 'pageCount', type: 'integer'),
        ], type: 'object'),
    ],
    type: 'object'
)]
class PaginateResponse
{
}
