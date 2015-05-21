<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <bugadani@gmail.com>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\AccessControl;

use LogicException;
use Miny\Application\Dispatcher;
use Miny\Factory\Container;
use Miny\HTTP\Response;
use Miny\Router\RouteGenerator;
use Miny\Utils\ArrayUtils;
use Modules\Annotation\Comment;
use UnexpectedValueException;

interface RequestHandlerInterface
{

    /**
     * @param Comment $comment
     *
     * @throws LogicException
     * @throws UnexpectedValueException
     * @return Response
     */
    public function create(Comment $comment);
}
