<?php

declare(strict_types=1);

namespace App\Http\Response\Responder\Error;

use App\Core\Validator\ValidateException;
use App\Http\Response\Responder;
use OpenApi\Attributes as OA;
use yii\base\Model;

#[OA\Schema(
    schema: self::class,
    properties: [
        new OA\Property(
            property: 'errors',
            properties: [
                new OA\Property(property: 'data', type: 'object'),
                new OA\Property(property: 'message', type: 'string'),
            ]
        ),
    ],
    type: 'object'
)]
class ViolationResponder implements Responder
{
    /**
     * @param Model|ValidateException $data
     *
     * @return array[]
     */
    public function transform($data): array
    {
        return [
            'errors' => [
                'message' => $data instanceof ValidateException ? $data->getMessage() : 'Invalid data.',
                'data' => $data->getErrors(),
            ],
        ];
    }
}
