<?php

declare(strict_types=1);

namespace App\Models\User\Entity\User\Task;

use App\Models\User\Entity\User\ActiveQuery\UserQuery;
use App\Models\User\Entity\User\Task\ActiveQuery\TaskQuery;
use App\Models\User\Entity\User\Types as UserTypes;
use App\Models\User\Entity\User\User;
use App\Models\User\Exception\DomainException;
use DateTime;
use Exception;
use Saschati\ValueObject\Behaviors\MixedTypeBehavior;
use Saschati\ValueObject\Types\Specials\TimestampType;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property Types\Id     $id
 * @property string       $title
 * @property string       $description
 * @property Types\Status $status
 * @property UserTypes\Id $user_id
 * @property DateTime     $started_at
 *
 * @property User $user
 */
class Task extends ActiveRecord
{
    public function behaviors(): array
    {
        return [
            'vo' => [
                'class' => MixedTypeBehavior::class,
                'attributes' => [
                    'id' => Types\Id::class,
                    'status' => Types\Status::class,
                    'user_id' => UserTypes\Id::class,
                    'started_at' => TimestampType::class,
                ],
            ],
        ];
    }

    public static function tableName(): string
    {
        return '{{%user_tasks}}';
    }

    public static function find(): ActiveQuery|TaskQuery
    {
        return new TaskQuery(static::class);
    }

    /**
     * @throws Exception
     */
    public static function createNew(
        Types\Id $id,
        string $title,
        string $description,
        UserTypes\Id $userId,
        DateTime $startedAt
    ): self {
        $entity = new self();

        $entity->id = $id;
        $entity->title = $title;
        $entity->description = $description;
        $entity->user_id = $userId;
        $entity->started_at = $startedAt;
        $entity->status = Types\Status::New;

        return $entity;
    }

    public function edit(string $title, string $description, Types\Status $status, DateTime $startedAt): void
    {
        if (!$this->status->isNew() && $status === Types\Status::New) {
            throw new DomainException('A processed task cannot change to a new status.');
        }

        $this->title = $title;
        $this->status = $status;
        $this->description = $description;
        $this->started_at = $startedAt;
    }

    public function remove(): void
    {
        if ($this->status->isNew() === false) {
            throw new DomainException('Only unprocessed tasks are allowed to be deleted.');
        }
    }

    public function getUser(): UserQuery|ActiveQuery
    {
        return $this->hasOne(User::class, ['user_id' => 'id']);
    }
}
