<?php

declare(strict_types=1);

namespace App\Models\User\Entity\User;

use App\Models\User\Entity\User\ActiveQuery\UserQuery;
use App\Models\User\Entity\User\Repository\TaskQueries;
use App\Models\User\Entity\User\Repository\TaskRepository;
use App\Models\User\Entity\User\Task\ActiveQuery\TaskQuery;
use App\Models\User\Entity\User\Task\Task;
use App\Models\User\Entity\User\Task\Types as TaskTypes;
use App\Models\User\Infrastructure\AuthKeyGenerator;
use App\Models\User\Infrastructure\Password\PasswordHasher;
use DateTime;
use elisdn\hybrid\AuthRoleModelInterface;
use Exception;
use Saschati\ValueObject\Behaviors\MixedTypeBehavior;
use Saschati\ValueObject\Types\Specials\TimestampType;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * @property Types\Id       $id
 * @property Types\Username $username
 * @property Types\Name     $name
 * @property Types\Role     $role
 * @property Types\Email    $email
 * @property string         $password_hash
 * @property string         $auth_key
 * @property Types\Status   $status
 * @property DateTime       $registered_at
 *
 * @property Task[] $tasks
 */
class User extends ActiveRecord implements IdentityInterface, AuthRoleModelInterface
{
    public function behaviors(): array
    {
        return [
            'vo' => [
                'class' => MixedTypeBehavior::class,
                'attributes' => [
                    'id' => Types\Id::class,
                    'username' => Types\Username::class,
                    'email' => Types\Email::class,
                    'name' => Types\Name::class,
                    'status' => Types\Status::class,
                    'role' => Types\Role::class,
                    'registered_at' => TimestampType::class,
                ],
            ],
        ];
    }

    public static function tableName(): string
    {
        return '{{%user_users}}';
    }

    public static function find(): ActiveQuery|UserQuery
    {
        return new UserQuery(static::class);
    }

    /**
     * @param mixed|string $token
     * @param mixed|string $type
     */
    public static function findIdentityByAccessToken($token, $type = null): ActiveRecord|array|User
    {
        return static::find()->where(['auth_key' => $token])->one();
    }

    /**
     * @param mixed $id
     */
    public static function findAuthRoleIdentity($id): AuthRoleModelInterface|User|null
    {
        return static::findOne($id);
    }

    /**
     * @param mixed|string $roleName
     *
     * @return ActiveRecord[]
     */
    public static function findAuthIdsByRoleName($roleName): array
    {
        return static::find()->where(['role' => $roleName])->select('id')->column();
    }

    public function getAuthRoleNames(): array
    {
        return (array)$this->role;
    }

    /**
     * @param mixed|string $roleName
     */
    public function addAuthRoleName($roleName): void
    {
        $this->updateAttributes(['role' => $this->role = $roleName]);
    }

    /**
     * @param mixed|string $roleName
     */
    public function removeAuthRoleName($roleName): void
    {
        $this->updateAttributes(['role' => $this->role = null]);
    }

    public function clearAuthRoleNames(): void
    {
        $this->updateAttributes(['role' => $this->role = null]);
    }

    /**
     * @param int|string $id
     */
    public static function findIdentity($id): ActiveRecord|IdentityInterface|User|null
    {
        return static::find()->where(['id' => $id])->one();
    }

    public function getId(): string
    {
        return (string)$this->getPrimaryKey();
    }

    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    /**
     * @param mixed|string $authKey
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @throws Exception
     */
    public static function joinByEmail(
        Types\Id $id,
        Types\Email $email,
        Types\Username $username,
        Types\Name $name,
        string $password,
        PasswordHasher $passwordHasher,
        AuthKeyGenerator $authKeyGenerator
    ): self {
        $entity = new self();

        $entity->id = $id;
        $entity->username = $username;
        $entity->email = $email;
        $entity->name = $name;

        $entity->password_hash = $passwordHasher->hash($password);
        $entity->auth_key = $authKeyGenerator->generate();

        $entity->status = Types\Status::Active;
        $entity->role = Types\Role::User;

        $entity->registered_at = new DateTime();

        return $entity;
    }

    public function editProfile(
        Types\Email $email,
        Types\Username $username,
        Types\Name $name,
        string $password,
        PasswordHasher $passwordHasher,
        AuthKeyGenerator $authKeyGenerator
    ): void {
        $this->username = $username;
        $this->email = $email;
        $this->name = $name;

        $this->password_hash = $passwordHasher->hash($password);
        $this->auth_key = $authKeyGenerator->generate();
    }

    /**
     * @throws Exception
     */
    public function addTask(
        TaskTypes\Id $id,
        string $title,
        string $description,
        DateTime $startedAt,
        TaskRepository $repository,
    ): void {
        $task = Task::createNew($id, $title, $description, $this->id, $startedAt);

        $repository->addTask($task);
    }

    /**
     * @throws Exception
     */
    public function editTask(
        TaskTypes\Id $id,
        string $title,
        string $description,
        TaskTypes\Status $status,
        DateTime $startedAt,
        TaskQueries $queries,
        TaskRepository $repository,
    ): void {
        $task = $queries->getTask($this->id, $id);

        $task->edit($title, $description, $status, $startedAt);

        $repository->addTask($task);
    }

    /**
     * @throws Exception
     */
    public function removeTask(
        TaskTypes\Id $id,
        TaskQueries $queries,
        TaskRepository $repository,
    ): void {
        $task = $queries->getTask($this->id, $id);

        $task->remove();

        $repository->removeTask($task);
    }

    public function getTasks(): TaskQuery|ActiveQuery
    {
        return $this->hasMany(Task::class, ['user_id' => 'id']);
    }
}
