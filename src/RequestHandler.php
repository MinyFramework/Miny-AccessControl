<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\AccessControl;

use Miny\Application\Dispatcher;
use Miny\Factory\Factory;
use Miny\HTTP\Response;
use Miny\Routing\RouteGenerator;
use Miny\Utils\ArrayUtils;
use Modules\Annotation\Comment;

class RequestHandler
{
    /**
     * @var Factory
     */
    private $factory;

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

    /**
     * @param Dispatcher $dispatcher
     */
    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param RouteGenerator $routeGenerator
     */
    public function setRouteGenerator(RouteGenerator $routeGenerator)
    {
        $this->routeGenerator = $routeGenerator;
    }

    /**
     * @param Factory $factory
     */
    public function setFactory(Factory $factory)
    {
        $this->factory = $factory;
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

        $mainRequest = $this->factory->get('request');
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
