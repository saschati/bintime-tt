<?php

declare(strict_types=1);

namespace App\Core\Rbac;

use DomainException;
use Exception;
use yii\rbac\ManagerInterface;

class RoleManager
{
    public function __construct(private ManagerInterface $manager)
    {
    }

    /**
     * @throws Exception
     */
    public function assign(mixed $userId, mixed $name): void
    {
        $am = $this->manager;
        $am->revokeAll($userId);

        $role = $am->getRole($name);
        if ($role === null) {
            throw new DomainException(sprintf('Role "%s" does not exist.', $name));
        }

        $am->revokeAll($userId);
        $am->assign($role, $userId);
    }

    /**
     * @throws Exception
     */
    public function assigns(mixed $userId, mixed $names): void
    {
        $this->checkRolesExists($names);

        $this->manager->revokeAll($userId);
        foreach ($names as $name) {
            $this->manager->assign($this->manager->getRole($name), $userId);
        }
    }

    public function revokeAll(mixed $userId): void
    {
        $this->manager->revokeAll($userId);
    }

    /**
     * @throws DomainException
     */
    protected function checkRolesExists(array $names): bool
    {
        foreach ($names as $name) {
            $role = $this->manager->getRole($name);
            if ($role === null) {
                throw new DomainException(sprintf('Role "%s" does not exist.', $name));
            }
        }

        return true;
    }
}
