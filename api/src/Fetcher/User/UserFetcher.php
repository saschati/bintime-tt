<?php

declare(strict_types=1);

namespace App\Fetcher\User;

use App\Core\Db\DataProvider\SqlDataProvider;
use App\Core\Db\Query;
use App\Models\User\Entity\User\User;
use yii\db\Expression;
use yii\web\NotFoundHttpException;

class UserFetcher
{
    public function getAll(): SqlDataProvider
    {
        $query = (new Query())->select(
            [
                'id' => 'id',
                'username' => 'username',
                'email' => 'email',
                'name' => 'name',
                'registeredAt' => 'TO_CHAR(u.registered_at, \'yyyy-mm-dd hh24:mi\')',
            ]
        );

        $query->from(['u' => User::tableName()]);

        return SqlDataProvider::fromQuery(
            $query,
            pagination: [],
            sort: [
                'defaultOrder' => ['registeredAt' => SORT_DESC],
                'attributes' => [
                    'registeredAt' => [
                        'asc' => ['u.registered_at' => SORT_ASC],
                        'desc' => ['u.registered_at' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                    'firstName' => [
                        'asc' => new Expression('u.name->>\'firstName\' ASC'),
                        'desc' => new Expression('u.name->>\'firstName\' DESC'),
                        'default' => SORT_DESC,
                    ],
                    'lastName' => [
                        'asc' => new Expression('u.name->>\'lastName\' ASC'),
                        'desc' => new Expression('u.name->>\'lastName\' DESC'),
                        'default' => SORT_DESC,
                    ],
                    'email' => [
                        'asc' => ['u.email' => SORT_ASC],
                        'desc' => ['u.email' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                ],
            ],
        );
    }

    /**
     * @throws NotFoundHttpException
     */
    public function get(string $id): array
    {
        $query = (new Query())->select(
            [
                'id' => 'u.id',
                'username' => 'u.username',
                'email' => 'u.email',
                'name' => 'u.name',
                'registeredAt' => 'TO_CHAR(u.registered_at, \'yyyy-mm-dd hh24:mi\')',
            ]
        );

        $query->from(['u' => User::tableName()])
            ->where(['id' => $id]);

        $entity = $query->one();

        if (empty($entity) === true) {
            throw new NotFoundHttpException('User not found.');
        }

        return $entity;
    }
}
