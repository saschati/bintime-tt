<?php

declare(strict_types=1);

namespace App\Http\Forms\User\User;

use OpenApi\Attributes as OA;
use yii\base\Model;

#[OA\Schema(
    schema: self::class,
    required: [
        'firstName',
        'lastName',
    ],
)]
class NameType extends Model
{
    #[OA\Property()]
    public string $firstName = '';
    #[OA\Property()]
    public string $lastName = '';

    public function rules(): array
    {
        return [
            [
                [
                    'firstName',
                    'lastName',
                ],
                'required',
            ],
            [
                [
                    'firstName',
                    'lastName',
                ],
                'string',
                'max' => 100,
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'firstName' => 'First name',
            'lastName' => 'Last name',
        ];
    }
}
