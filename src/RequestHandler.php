<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\AccessControl;

use Miny\Application\Dispatcher;
use Miny\Factory\Container;
use Miny\Factory\Factory;
use Miny\HTTP\Response;
use Miny\Routing\RouteGenerator;
use Miny\Routing\Router;
use Miny\Utils\ArrayUtils;
use Modules\Annotation\Comment;

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

    function __construct(Dispatcher $dispatcher, Container $factory, Router $routeGenerator)
    {
        $this->dispatcher     = $dispatcher;
        $this->container        = $factory;
        $this->routeGenerator = $routeGenerator;
    }

    public function setDefaultRedirection($path, array $params = array())
    {
        $this->defaultRoute  = $path;
        $this->defaultParams = $params;
    }

    /**
     * @param \Modules\Annotation\Comment $comment
     *
     * @throws \UnexpectedValueException
     * @return Response
     */
    public function create(Comment $comment)
    {
        $routeName       = ArrayUtils::getByPath($comment, 'unauthorized', $this->defaultRoute);
        $routeParameters = ArrayUtils::getByPath($comment, 'unauthorizedParameters', $this->defaultParams);

        $url = $this->routeGenerator->generate($routeName, $routeParameters);

        $mainRequest = $this->container->get('\\Miny\\HTTP\\Request');
        if ($mainRequest->isSubRequest() && $url === $mainRequest->url) {
            throw new \UnexpectedValueException('This redirection leads to an infinite loop.');
        }

        $request = $mainRequest->getSubRequest('GET', $url);

        $response = $this->dispatcher->dispatch($request);
        // on the response, set a 403 Unauthorized header
        $response->setCode(403);

        return $response;
    }
}
