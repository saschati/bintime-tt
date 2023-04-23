<?php

declare(strict_types=1);

namespace App\Http\Response\Responder\User\User;

use App\Http\Response\Responder;
use App\Models\User\Entity\User\Task\Types\Status;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: self::class,
    properties: [
        new OA\Property(property: 'status', type: 'string', enum: [Status::New, Status::InProgress, Status::Failed, Status::Finished]),
        new OA\Property(property: 'count', type: 'integer'),
    ],
    type: 'object'
)]
class TaskStatsResponder implements Responder
{
    /**
     * @param array $data
     *
     * @return array[]
     */
    public function transform($data): array
    {
        return [
            'status' => $data['status'],
            'count' => (int)$data['count'],
        ];
    }
}
