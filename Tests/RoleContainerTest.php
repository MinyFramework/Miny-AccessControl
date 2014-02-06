<?php

namespace Modules\AccessControl;

class RoleContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RoleContainer
     */
    private $container;

    public function setUp()
    {
        $this->container = new RoleContainer();
        $this->container->add('StringRole');
        $this->container->add(new Role('ObjectRole'));
        $this->container->addRoles(
            array(
                'AnotherStringRole',
                new Role('AnotherObjectRole')
            )
        );
    }

    public function testContainer()
    {
        $this->assertTrue($this->container->has('StringRole'));
        $this->assertTrue($this->container->has('ObjectRole'));
        $this->assertTrue($this->container->has('AnotherStringRole'));
        $this->assertTrue($this->container->has('AnotherObjectRole'));
        $this->assertFalse($this->container->has('FooRole'));
    }



    public function testGetAll()
    {
        $this->assertCount(4, $this->container->getAll());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $role must be an instance of Role or a string.
     */
    public function testHasInvalidName()
    {
        $this->container->has(5);
    }

    public function testRemoveRole()
    {
        $this->assertTrue($this->container->has('StringRole'));
        $this->container->remove('StringRole');
        $this->assertFalse($this->container->has('StringRole'));
    }
}
