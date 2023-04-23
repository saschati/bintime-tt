<?php

declare(strict_types=1);

namespace App\Http\Forms\User\User\Task;

use App\Models\User\Entity\User\Task\Types\Status;
use OpenApi\Attributes as OA;
use yii\base\Model;

#[OA\Schema(
    schema: self::class,
    required: [
        'title',
        'description',
        'status',
        'startedAt',
    ],
)]
class EditForm extends Model
{
    #[OA\Property()]
    public string $title = '';
    #[OA\Property()]
    public string $description = '';
    #[OA\Property(enum: [Status::New, Status::InProgress, Status::Failed, Status::Finished])]
    public string $status = '';
    #[OA\Property(example: '2023-04-22 15:03')]
    public string $startedAt = '';

    public function rules(): array
    {
        return [
            [
                [
                    'title',
                    'description',
                    'status',
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
                ['status'],
                'in',
                'range' => Status::values(),
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
            'status' => 'Status',
            'startedAt' => 'Start date and time',
        ];
    }
}
