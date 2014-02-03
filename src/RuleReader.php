<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\AccessControl;

use Miny\Controller\BaseController;
use Modules\Annotation\Annotation;
use Modules\Annotation\Comment;

class RuleReader
{
    /**
     * @var Comment
     */
    private $lastComment;

    /**
     * @var Annotation
     */
    private $annotation;

    /**
     * @param Annotation $annotation
     */
    public function __construct(Annotation $annotation)
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

    public function readAction(BaseController $controller, $action)
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
        $roles = $comment['role'];
        if (!is_array($roles)) {
            $roles = array($roles);
        }

        return $roles;
    }

    public function getLastComment()
    {
        return $this->lastComment;
    }
}
