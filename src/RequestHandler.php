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

class RequestHandler
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var RouteGenerator
     */
    private $routeGenerator;

    /**
     * @var Dispatcher
     */
    private $dispatcher;
    private $defaultRoute;
    private $defaultParams = array();

    public function __construct(
        Dispatcher $dispatcher,
        Container $factory,
        RouteGenerator $routeGenerator
    ) {
        $this->dispatcher     = $dispatcher;
        $this->container      = $factory;
        $this->routeGenerator = $routeGenerator;
    }

    public function setDefaultRedirection($path, array $params = array())
    {
        $this->defaultRoute  = $path;
        $this->defaultParams = $params;
    }

    /**
     * @param Comment $comment
     *
     * @throws LogicException
     * @throws UnexpectedValueException
     * @return Response
     */
    public function create(Comment $comment)
    {
        $routeName       = ArrayUtils::get($comment, 'unauthorized', $this->defaultRoute);
        $routeParameters = ArrayUtils::get(
            $comment,
            'unauthorizedParameters',
            $this->defaultParams
        );

        if (!isset($routeName)) {
            throw new LogicException('No redirection URL has been set.');
        }
        $url = $this->routeGenerator->generate($routeName, $routeParameters);

        $mainRequest = $this->container->get('\\Miny\\HTTP\\Request');
        if ($mainRequest->isSubRequest() && $url === $mainRequest->getUrl()) {
            throw new UnexpectedValueException('This redirection leads to an infinite loop.');
        }

        $request = $mainRequest->getSubRequest('GET', $url);

        $response = $this->dispatcher->dispatch($request);
        // on the response, set a 403 Unauthorized header
        $response->setCode(403);

        return $response;
    }
}
