<?php

namespace Modules\AccessControl;

use Annotiny\Comment;
use Miny\HTTP\Request;
use Miny\HTTP\Response;

class RequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequestHandler
     */
    private $handler;
    private $factoryMock;
    private $dispatcherMock;
    private $routeMock;

    public function setUp()
    {
        $this->factoryMock = $this->getMockBuilder('Miny\Factory\Container')
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->dispatcherMock = $this->getMockBuilder('Miny\Application\Dispatcher')
            ->disableOriginalConstructor()
            ->getMock();

        $this->routeMock = $this->getMockBuilder('Miny\Router\RouteGenerator')
            ->disableOriginalConstructor()
            ->setMethods(['generate'])
            ->getMock();

        $this->handler = new RequestHandler($this->dispatcherMock, $this->factoryMock, $this->routeMock);
        $this->handler->setDefaultRedirection('path', ['param1', 'param2']);
    }

    public function testThatAnUnauthorizedHeaderIsSet()
    {
        $request = new Request('get', 'url');

        $this->factoryMock
            ->expects($this->once())
            ->method('get')
            ->with('\\Miny\\HTTP\\Request')
            ->will($this->returnValue($request));

        $this->routeMock
            ->expects($this->once())
            ->method('generate')
            ->with('path', ['param1', 'param2'])
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
            ->with('\\Miny\\HTTP\\Request')
            ->will($this->returnValue($request));

        $this->routeMock
            ->expects($this->once())
            ->method('generate')
            ->with('fooPath', ['fooParam'])
            ->will($this->returnArgument(0));

        $this->dispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->will($this->returnValue(new Response()));

        $comment = new Comment('description');
        $comment->add('unauthorized', 'fooPath');
        $comment->add('unauthorizedParameters', ['fooParam']);

        $this->handler->create($comment);
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
            ->with('\\Miny\\HTTP\\Request')
            ->will($this->returnValue($request));

        $this->routeMock
            ->expects($this->once())
            ->method('generate')
            ->will($this->returnArgument(0));

        $comment = new Comment('description');
        $comment->add('unauthorized', 'fooPath');

        $this->handler->create($comment);
    }

}
