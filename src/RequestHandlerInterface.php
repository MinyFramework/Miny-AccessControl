<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <bugadani@gmail.com>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\AccessControl;

use Miny\HTTP\Response;
use Modules\Annotation\Comment;

interface RequestHandlerInterface
{

    /**
     * @param Comment $comment
     *
     * @return Response
     */
    public function create(Comment $comment);
}
