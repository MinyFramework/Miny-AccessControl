<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <bugadani@gmail.com>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\AccessControl;

use Miny\Controller\Controller;
use Miny\Controller\Events\ControllerLoadedEvent;

class AccessControl
{
    /**
     * @var RequestHandler
     */
    private $requestHandler;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var RuleReader
     */
    private $reader;

    public function __construct(RuleReader $reader, RequestHandler $requestHandler)
    {
        $this->reader         = $reader;
        $this->requestHandler = $requestHandler;
    }

    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    public function onControllerLoaded(ControllerLoadedEvent $event)
    {
        if (!$this->isAuthorized($event->getController(), $event->getAction())) {
            // unauthorized
            return $this->requestHandler->create($this->reader->getLastComment());
        }
    }

    /**
     * @param $controller
     * @param $action
     *
     * @return bool
     */
    private function isAuthorized($controller, $action)
    {
        // check controller
        $roles = $this->reader->readController($controller);
        if (!$this->checkAuthorized($roles)) {
            return false;
        }

        // check action
        if ($controller instanceof Controller) {
            $roles = $this->reader->readAction($controller, $action);
            if (!$this->checkAuthorized($roles)) {
                return false;
            }
        }

        return true;
    }

    private function checkAuthorized(array $roles)
    {
        if (empty($roles)) {
            return true;
        }
        if(!isset($this->user)) {
            return false;
        }
        $container = $this->user->getRoleContainer();
        foreach ($roles as $role) {
            if ($container->has($role)) {
                return true;
            }
        }

        return false;
    }

}
