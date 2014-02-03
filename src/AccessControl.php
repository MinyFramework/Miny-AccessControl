<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\AccessControl;

use Miny\Controller\BaseController;

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

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @param RequestHandler $requestHandler
     */
    public function setRequestHandler(RequestHandler $requestHandler)
    {
        $this->requestHandler = $requestHandler;
    }

    /**
     * @param \Modules\AccessControl\RuleReader $reader
     */
    public function setReader(RuleReader $reader)
    {
        $this->reader = $reader;
    }

    public function onControllerLoaded($controller, $action)
    {
        if (!$this->isAuthorized($controller, $action)) {
            // unauthorized
            return $this->requestHandler->createRequest($this->reader->getLastComment());
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
        if ($controller instanceof BaseController) {
            $roles = $this->reader->readAction($controller, $action);
            if (!$this->checkAuthorized($roles)) {
                return false;
            }
        }

        return true;
    }

    private function checkAuthorized(array $roles)
    {
        $container = $this->user->getRoleContainer();
        if (empty($roles)) {
            return true;
        }
        foreach ($roles as $role) {
            if ($container->has($role)) {
                return true;
            }
        }

        return false;
    }

}
