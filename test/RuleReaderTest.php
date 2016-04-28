<?php

namespace Modules\AccessControl;

use Annotiny\AnnotationReader;
use Annotiny\Comment;

class RuleReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleReader
     */
    private $reader;

    /**
     * @var AnnotationReader
     */
    private $annotationMock;

    public function setUp()
    {
        $this->annotationMock = $this->getMockBuilder('Annotiny\AnnotationReader')
            ->setMethods(['readClass', 'readFunction', 'readMethod'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->reader = new RuleReader($this->annotationMock);
    }

    public function testLastCommentShouldBeIdenticalToTheReadComment()
    {
        $comment = new Comment('description');
        $comment->add('role', 'foo');

        $this->annotationMock
            ->expects($this->once())
            ->method('readFunction')
            ->with(
                $this->callback(
                    function ($function) {
                        return $function instanceof \Closure;
                    }
                )
            )
            ->will($this->returnValue($comment));

        $this->reader->readController(
            function () {
            }
        );
        $this->assertSame($comment, $this->reader->getLastComment());
    }

    public function testThatTheCorrectMethodsAreCalled()
    {
        $comment = new Comment('description');

        $baseControllerStub = $this->getMockBuilder('Miny\Controller\Controller')
            ->setMethods(['fooAction'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->annotationMock
            ->expects($this->once())
            ->method('readClass')
            ->with($baseControllerStub)
            ->will($this->returnValue($comment));

        $this->annotationMock
            ->expects($this->once())
            ->method('readMethod')
            ->with($baseControllerStub, 'fooAction')
            ->will($this->returnValue($comment));

        $this->reader->readController($baseControllerStub);
        $this->reader->readAction($baseControllerStub, 'foo');
    }

    public function testThatEmptyArrayIsAlwaysReturned()
    {
        $comment = new Comment('description');
        $this->annotationMock
            ->expects($this->once())
            ->method('readFunction')
            ->with(
                $this->callback(
                    function ($function) {
                        return $function instanceof \Closure;
                    }
                )
            )
            ->will($this->returnValue($comment));

        $roles = $this->reader->readController(
            function () {
            }
        );
        $this->assertInternalType('array', $roles);
        $this->assertEmpty($roles);
    }

    public function testSingleRoleShouldBeConvertedToArray()
    {
        $comment = new Comment('description');
        $comment->add('role', 'foo');

        $this->annotationMock
            ->expects($this->once())
            ->method('readFunction')
            ->with(
                $this->callback(
                    function ($function) {
                        return $function instanceof \Closure;
                    }
                )
            )
            ->will($this->returnValue($comment));

        $roles = $this->reader->readController(
            function () {
            }
        );
        $this->assertInternalType('array', $roles);
        $this->assertCount(1, $roles);
    }
}
