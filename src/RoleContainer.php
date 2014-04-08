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
     * @return Role[]
     */
    public function getAll()
    {
        return array_keys($this->roles);
    }
}
