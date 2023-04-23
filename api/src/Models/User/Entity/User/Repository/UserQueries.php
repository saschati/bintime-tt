<?php

declare(strict_types=1);

namespace App\Models\User\Entity\User\Repository;

use App\Core\Db\AbstractQueries;
use App\Core\Db\ModelNotFoundException;
use App\Models\User\Entity\User\Task\Task;
use App\Models\User\Entity\User\Task\Types as TaskTypes;
use App\Models\User\Entity\User\Types as UserTypes;
use App\Models\User\Entity\User\Types\Email;
use App\Models\User\Entity\User\Types\Username;
use App\Models\User\Entity\User\User;
use yii\db\ActiveRecord;

class UserQueries extends AbstractQueries implements TaskQueries
{
    public function hasEmail(Email $email): bool
    {
        return $this->hasBy('email', (string)$email);
    }

    public function hasUsername(Username $username): bool
    {
        return $this->hasBy('username', (string)$username);
    }

    /**
     * @throws ModelNotFoundException
     */
    public function getTask(UserTypes\Id $userId, TaskTypes\Id $id): Task
    {
        /** @var Task $task */
        return Task::find()
            ->where(['user_id' => (string)$userId])
            ->andWhere(['id' => (string)$id])
            ->firstOrFail();
    }

    protected function model(): ActiveRecord|string|User
    {
        return User::class;
    }
}
