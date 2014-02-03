<?php

namespace Modules\AccessControl;

use Miny\HTTP\Request;
use Miny\HTTP\Response;
use Modules\Annotation\Comment;

class RequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequestHandler
     */
    private $handler;
    private $factoryMock;
    private $dispatcherMock;
    private $routeGeneratorMock;

    public function setUp()
    {
        $this->factoryMock = $this->getMockBuilder('Miny\Factory\Factory')
            ->setMethods(array('get'))
            ->getMock();

        $this->dispatcherMock = $this->getMockBuilder('Miny\Application\Dispatcher')
            ->disableOriginalConstructor()
            ->getMock();

        $this->routeGeneratorMock = $this->getMockBuilder('Miny\Routing\RouteGenerator')
            ->disableOriginalConstructor()
            ->setMethods(array('generate'))
            ->getMock();

        $this->handler = new RequestHandler();
        $this->handler->setDefaultRedirection('path', array('param1', 'param2'));
        $this->handler->setFactory($this->factoryMock);
        $this->handler->setDispatcher($this->dispatcherMock);
        $this->handler->setRouteGenerator($this->routeGeneratorMock);
    }

    public function testThatAnUnauthorizedHeaderIsSet()
    {
        $request = new Request('get', 'url');

        $this->factoryMock
            ->expects($this->once())
            ->method('get')
            ->with('request')
            ->will($this->returnValue($request));

        $this->routeGeneratorMock
            ->expects($this->once())
            ->method('generate')
            ->with('path', array('param1', 'param2'))
            ->will($this->returnArgument(0));

        $this->dispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->will($this->returnValue(new Response()));

        $response = $this->handler->create(new Comment('description'));

        $this->assertTrue($response->isCode(403));
    }

    public function testThatCommentIsRead()
    {
        $request = new Request('get', 'url');

        $this->factoryMock
            ->expects($this->once())
            ->method('get')
            ->with('request')
            ->will($this->returnValue($request));

        $this->routeGeneratorMock
            ->expects($this->once())
            ->method('generate')
            ->with('fooPath', array('fooParam'))
            ->will($this->returnArgument(0));

        $this->dispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->will($this->returnValue(new Response()));

        $this->handler->create(
            new Comment('description', array(
                'unauthorized'           => 'fooPath',
                'unauthorizedParameters' => array('fooParam')
            ))
        );
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage This redirection leads to an infinite loop.
     */
    public function testThatCyclicRedirectionIsDetected()
    {
        $sourceRequest = new Request('get', 'foo');
        $request       = $sourceRequest->getSubRequest('get', 'fooPath');

        $this->factoryMock
            ->expects($this->once())
            ->method('get')
            ->with('request')
            ->will($this->returnValue($request));

        $this->routeGeneratorMock
            ->expects($this->once())
            ->method('generate')
            ->will($this->returnArgument(0));

        $this->handler->create(
            new Comment('description', array(
                'unauthorized' => 'fooPath'
            ))
        );
    }

}
