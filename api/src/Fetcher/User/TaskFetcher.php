<?php

declare(strict_types=1);

namespace App\Fetcher\User;

use App\Core\Db\DataProvider\SqlDataProvider;
use App\Core\Db\Query;
use App\Models\User\Entity\User\Task\Task;
use yii\web\NotFoundHttpException;

class TaskFetcher
{
    public function getAll(string $userId): SqlDataProvider
    {
        $query = (new Query())->select(
            [
                'id' => 't.id',
                'title' => 't.title',
                'description' => 't.description',
                'status' => 't.status',
                'startedAt' => 'TO_CHAR(t.started_at, \'yyyy-mm-dd hh24:mi\')',
            ]
        );

        $query->from(['t' => Task::tableName()])
            ->where(['t.user_id' => $userId]);

        return SqlDataProvider::fromQuery(
            $query,
            pagination: [],
            sort: [
                'defaultOrder' => ['startedAt' => SORT_DESC],
                'attributes' => [
                    'startedAt' => [
                        'asc' => ['t.started_at' => SORT_ASC],
                        'desc' => ['t.started_at' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                    'title' => [
                        'asc' => ['t.title' => SORT_ASC],
                        'desc' => ['t.title' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                    'description' => [
                        'asc' => ['t.description' => SORT_ASC],
                        'desc' => ['t.description' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                ],
            ],
        );
    }

    /**
     * @throws NotFoundHttpException
     */
    public function get(string $id, string $taskId): array
    {
        $query = (new Query())->select(
            [
                'id' => 't.id',
                'title' => 't.title',
                'description' => 't.description',
                'status' => 't.status',
                'startedAt' => 'TO_CHAR(t.started_at, \'yyyy-mm-dd hh24:mi\')',
            ]
        );

        $query->from(['t' => Task::tableName()])
            ->where(['t.user_id' => $id])
            ->andWhere(['t.id' => $taskId]);

        $entity = $query->one();

        if (empty($entity) === true) {
            throw new NotFoundHttpException('Task not found.');
        }

        return $entity;
    }

    public function getStats(string $userId = null): SqlDataProvider
    {
        $query = (new Query())->select(
            [
                'status' => 't.status',
                'count' => 'COUNT(t.id)',
            ]
        );

        $query->from(['t' => Task::tableName()])
            ->andFilterWhere(['t.user_id' => $userId])
            ->groupBy('t.status');

        return SqlDataProvider::fromQuery($query);
    }
}
