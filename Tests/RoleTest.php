<?php

namespace Modules\AccessControl;

class RoleTest extends \PHPUnit_Framework_TestCase
{

    public function testRole()
    {
        $name = 'FooBar';
        $role = new Role($name);

        $this->assertEquals($name, $role->getRole());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Role must be a string
     */
    public function testInvalidRoleName()
    {
        new Role(42);
    }
}
