<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\AccessControl;

class Role
{
    /**
     * @var string
     */
    private $role;

    /**
     * @param string $role
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($role)
    {
        if (!is_string($role)) {
            throw new \InvalidArgumentException('Role must be a string');
        }
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }
}
