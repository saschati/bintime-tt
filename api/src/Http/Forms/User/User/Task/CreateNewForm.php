<?php

declare(strict_types=1);

namespace App\Http\Forms\User\User\Task;

use OpenApi\Attributes as OA;
use yii\base\Model;

#[OA\Schema(
    schema: self::class,
    required: [
        'title',
        'description',
        'startedAt',
    ],
)]
class CreateNewForm extends Model
{
    #[OA\Property()]
    public string $title = '';
    #[OA\Property()]
    public string $description = '';
    #[OA\Property(example: '2023-04-22 15:03')]
    public string $startedAt = '';

    public function rules(): array
    {
        return [
            [
                [
                    'title',
                    'description',
                    'startedAt',
                ],
                'required',
            ],
            [
                ['title'],
                'string',
                'max' => 255,
            ],
            [
                ['description'],
                'string',
            ],
            [
                [
                    'startedAt',
                ],
                'date',
                'format' => 'php:!Y-m-d H:i',
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
            'startedAt' => 'Start date and time',
        ];
    }
}
