<?php
/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\AccessControl;

class RoleContainer
{
    /**
     * @var Role[]
     */
    private $roles;

    public function __construct()
    {
        $this->roles = array();
    }

    /**
     * @param string|Role $role
     */
    public function add($role)
    {
        if (!$role instanceof Role) {
            $role = new Role($role);
        }
        $this->roles[$role->getRole()] = $role;
    }

    /**
     * @param array $roles
     */
    public function addRoles(array $roles)
    {
        foreach ($roles as $role) {
            $this->add($role);
        }
    }

    /**
     * @param string|Role $role
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function has($role)
    {
        if ($role instanceof Role) {
            $role = $role->getRole();
        } elseif (!is_string($role)) {
            throw new \InvalidArgumentException('$role must be an instance of Role or a string.');
        }

        return isset($this->roles[$role]);
    }

    /**
     * @param string|Role $role
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function remove($role)
    {
        if ($role instanceof Role) {
            $role = $role->getRole();
        } elseif (!is_string($role)) {
            throw new \InvalidArgumentException('$role must be an instance of Role or a string.');
        }

        unset($this->roles[$role]);
    }

    /**
     * @return Role[]
     */
    public function getAll()
    {
        return $this->roles;
    }


}
