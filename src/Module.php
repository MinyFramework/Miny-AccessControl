<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\AccessControl;

use Miny\Application\BaseApplication;
use Miny\Utils\ArrayUtils;

class Module extends \Miny\Modules\Module
{
    public function getDependencies()
    {
        return array('Annotation');
    }

    public function defaultConfiguration()
    {
        return array(
            'access_control' => array(
                'roles' => array()
            )
        );
    }

    public function init(BaseApplication $app)
    {
        $container          = $app->getContainer();
        $parameterContainer = $app->getParameterContainer();
        $container->addCallback(
            '\\Modules\\AccessControl\\RoleContainer',
            function (RoleContainer $roles) use (
                $parameterContainer
            ) {
                $roles->addRoles($parameterContainer['access_control']['roles']);
            }
        );

        $container->addCallback(
            '\\Modules\\AccessControl\\RequestHandler',
            function (RequestHandler $handler) use ($parameterContainer) {
                if (isset($parameterContainer['access_control']['redirect_route'])) {
                    $path   = $parameterContainer['access_control']['redirect_route'];
                    $params = ArrayUtils::getByPath(
                        $parameterContainer,
                        array('access_control', 'redirect_parameters'),
                        array()
                    );
                    $handler->setDefaultRedirection($path, $params);
                }
            }
        );
    }

    public function eventHandlers()
    {
        $factory        = $this->application->getContainer();
        $access_control = $factory->get(__NAMESPACE__ . '\\AccessControl');

        return array(
            'onControllerLoaded' => array($access_control),
        );
    }
}
