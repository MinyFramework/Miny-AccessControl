<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <bugadani@gmail.com>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\AccessControl;

use Miny\Controller\Controller;
use Modules\Annotation\AnnotationReader;
use Modules\Annotation\Comment;

class RuleReader
{
    /**
     * @var Comment
     */
    private $lastComment;

    /**
     * @var AnnotationReader
     */
    private $annotation;

    /**
     * @param AnnotationReader $annotation
     */
    public function __construct(AnnotationReader $annotation)
    {
        $this->annotation = $annotation;
    }

    public function readController($controller)
    {
        if ($controller instanceof \Closure) {
            $comment = $this->annotation->readFunction($controller);
        } else {
            $comment = $this->annotation->readClass($controller);
        }

        return $this->extract($comment);
    }

    public function readAction(Controller $controller, $action)
    {
        $comment = $this->annotation->readMethod($controller, $action . 'Action');

        return $this->extract($comment);
    }

    private function extract(Comment $comment)
    {
        $this->lastComment = $comment;
        if (!isset($comment['role'])) {
            return array();
        }

        return (array) $comment['role'];
    }

    public function getLastComment()
    {
        return $this->lastComment;
    }
}
