<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <daniel@bugadani.hu>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\AccessControl;

use Miny\Application\BaseApplication;

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
