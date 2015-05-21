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
        $this->container->addRoles(
            [
                'OtherStringRole',
                'AnotherStringRole',
            ]
        );
    }

    public function testContainer()
    {
        $this->assertTrue($this->container->has('StringRole'));
        $this->assertTrue($this->container->has('OtherStringRole'));
        $this->assertTrue($this->container->has('AnotherStringRole'));
        $this->assertFalse($this->container->has('FooRole'));
    }

    public function testGetAll()
    {
        $this->assertCount(3, $this->container->getAll());
    }

    public function testRemoveRole()
    {
        $this->assertTrue($this->container->has('StringRole'));
        $this->container->remove('StringRole');
        $this->assertFalse($this->container->has('StringRole'));
    }
}
