<?php

namespace Modules\AccessControl;

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
        $this->controllerStub = $baseControllerStub = $this->getMockBuilder('Miny\Controller\BaseController')
            ->setMethods(array('fooAction'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->readerMock = $this->getMockBuilder('\Modules\AccessControl\RuleReader')
            ->disableOriginalConstructor()
            ->getMock();

        $this->userStub = $this->getMockBuilder('\Modules\AccessControl\UserInterface')
            ->getMock();

        $this->requestHandlerMock = $this->getMockBuilder('\Modules\AccessControl\RequestHandler')
            ->setMethods(array('createRequest'))
            ->getMock();

        $container = new RoleContainer();
        $container->add('role_user');
        $this->userStub->expects($this->any())
            ->method('getRoleContainer')
            ->will($this->returnValue($container));

        $this->accessControl = new AccessControl();
        $this->accessControl->setReader($this->readerMock);
        $this->accessControl->setUser($this->userStub);
        $this->accessControl->setRequestHandler($this->requestHandlerMock);
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
            ->method('createRequest');

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
            ->method('createRequest');

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
            ->expects($this->never())
            ->method('readAction');

        $this->requestHandlerMock
            ->expects($this->once())
            ->method('createRequest');

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
            ->method('readAction')
            ->with($this->controllerStub, 'foo')
            ->will($this->returnValue(array('role_foo')));

        $this->requestHandlerMock
            ->expects($this->once())
            ->method('createRequest');

        $this->accessControl->onControllerLoaded($this->controllerStub, 'foo');
    }
}
