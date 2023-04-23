<?php

declare(strict_types=1);

namespace App\Http\Response\Responder\Error;

use App\Http\Response\Responder;
use OpenApi\Attributes as OA;
use yii\web\NotFoundHttpException;

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
class NotFoundResponder implements Responder
{
    /**
     * @param NotFoundHttpException $data
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
