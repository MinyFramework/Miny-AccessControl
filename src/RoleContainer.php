<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <bugadani@gmail.com>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\AccessControl;

class RoleContainer
{
    /**
     * @var string[]
     */
    private $roles;

    public function __construct()
    {
        $this->roles = [];
    }

    /**
     * @param string $role
     */
    public function add($role)
    {
        $this->roles[$role] = true;
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
     * @param string $role
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function has($role)
    {
        return isset($this->roles[$role]);
    }

    /**
     * @param string $role
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function remove($role)
    {
        unset($this->roles[$role]);
    }

    /**
     * @return string[]
     */
    public function getAll()
    {
        return array_keys($this->roles);
    }
}
