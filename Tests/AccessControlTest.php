<?php

namespace Modules\AccessControl;

use Modules\Annotation\Comment;

class AccessControlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AccessControl
     */
    private $accessControl;

    /**
     * @var RuleReader
     */
    private $readerMock;

    /**
     * @var UserInterface
     */
    private $userStub;
    private $requestHandlerMock;
    private $controllerStub;

    public function setUp()
    {
        $this->controllerStub = $baseControllerStub = $this->getMockBuilder('Miny\Controller\Controller')
            ->setMethods(array('fooAction'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->readerMock = $this->getMockBuilder('\Modules\AccessControl\RuleReader')
            ->setMethods(array('getLastComment', 'readController', 'readAction'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->userStub = $this->getMockBuilder('\Modules\AccessControl\UserInterface')
            ->getMock();

        $this->requestHandlerMock = $this->getMockBuilder('\Modules\AccessControl\RequestHandler')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();

        $container = new RoleContainer();
        $container->add('role_user');
        $this->userStub->expects($this->any())
            ->method('getRoleContainer')
            ->will($this->returnValue($container));

        $this->accessControl = new AccessControl($this->readerMock, $this->requestHandlerMock);
        $this->accessControl->setUser($this->userStub);
    }

    public function testThatNoRedirectionIsDoneIfNoRuleIsSet()
    {
        $this->readerMock
            ->expects($this->once())
            ->method('readController')
            ->with($this->controllerStub)
            ->will($this->returnValue(array()));

        $this->readerMock
            ->expects($this->once())
            ->method('readAction')
            ->with($this->controllerStub, 'foo')
            ->will($this->returnValue(array()));

        $this->requestHandlerMock
            ->expects($this->never())
            ->method('create');

        $this->accessControl->onControllerLoaded($this->controllerStub, 'foo');
    }

    public function testThatNoRedirectionIsDoneIfAuthorized()
    {
        $this->readerMock
            ->expects($this->once())
            ->method('readController')
            ->with($this->controllerStub)
            ->will($this->returnValue(array('role_user')));

        $this->readerMock
            ->expects($this->once())
            ->method('readAction')
            ->with($this->controllerStub, 'foo')
            ->will($this->returnValue(array('role_user')));

        $this->requestHandlerMock
            ->expects($this->never())
            ->method('create');

        $this->accessControl->onControllerLoaded($this->controllerStub, 'foo');
    }

    public function testThatRedirectionIsMadeOnControllerAuthorizationFailure()
    {
        $this->readerMock
            ->expects($this->once())
            ->method('readController')
            ->with($this->controllerStub)
            ->will($this->returnValue(array('role_foo')));

        $this->readerMock
            ->expects($this->once())
            ->method('getLastComment')
            ->will($this->returnValue(new Comment('description')));

        $this->readerMock
            ->expects($this->never())
            ->method('readAction');

        $this->requestHandlerMock
            ->expects($this->once())
            ->method('create');

        $this->accessControl->onControllerLoaded($this->controllerStub, 'foo');
    }

    public function testThatRedirectionIsMadeOnActionAuthorizationFailure()
    {
        $this->readerMock
            ->expects($this->once())
            ->method('readController')
            ->with($this->controllerStub)
            ->will($this->returnValue(array('role_user')));

        $this->readerMock
            ->expects($this->once())
            ->method('getLastComment')
            ->will($this->returnValue(new Comment('description')));

        $this->readerMock
            ->expects($this->once())
            ->method('readAction')
            ->with($this->controllerStub, 'foo')
            ->will($this->returnValue(array('role_foo')));

        $this->requestHandlerMock
            ->expects($this->once())
            ->method('create');

        $this->accessControl->onControllerLoaded($this->controllerStub, 'foo');
    }
}
