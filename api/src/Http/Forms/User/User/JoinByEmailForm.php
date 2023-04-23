<?php

declare(strict_types=1);

namespace App\Http\Forms\User\User;

use App\Http\Forms\CompositeForm;
use App\Models\User\Entity\User\User;
use OpenApi\Attributes as OA;
use yii\base\Model;

#[OA\Schema(
    schema: self::class,
    required: [
        'name',
        'username',
        'email',
        'password',
    ],
)]
class JoinByEmailForm extends Model implements CompositeForm
{
    #[OA\Property()]
    public NameType $name;
    #[OA\Property()]
    public string $username = '';
    #[OA\Property(format: 'email')]
    public string $email = '';
    #[OA\Property(format: 'password')]
    public string $password = '';

    public function rules(): array
    {
        return [
            [
                [
                    'name',
                    'username',
                    'email',
                    'password',
                ],
                'required',
            ],
            [
                'email',
                'email',
            ],
            [
                ['email'],
                'string',
                'max' => 255,
            ],
            [
                ['username'],
                'string',
                'max' => 255,
                'min' => 4,
            ],
            [
                ['password'],
                'string',
                'max' => 100,
                'min' => 6,
            ],
            [
                ['username'],
                'unique',
                'targetClass' => User::class,
            ],
            [
                ['email'],
                'unique',
                'targetClass' => User::class,
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Name',
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
        ];
    }

    public function forms(): array
    {
        return [
            'name',
        ];
    }
}
