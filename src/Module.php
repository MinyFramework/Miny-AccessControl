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
            'roles' => array()
        );
    }

    public function init(BaseApplication $app)
    {
        $container = $app->getContainer();

        $module = $this;
        $container->addCallback(
            '\\Modules\\AccessControl\\RoleContainer',
            function (RoleContainer $roles) use ($module) {
                $roles->addRoles($module->getConfiguration('roles'));
            }
        );

        $container->addCallback(
            '\\Modules\\AccessControl\\RequestHandler',
            function (RequestHandler $handler) use ($module) {
                if ($module->hasConfiguration('redirect_route')) {
                    $path = $module->getConfiguration('redirect_route');
                    if ($module->hasConfiguration('redirect_parameters')) {
                        $params = $module->getConfiguration('redirect_parameters');
                    } else {
                        $params = array();
                    }
                    $handler->setDefaultRedirection($path, $params);
                }
            }
        );
    }

    public function eventHandlers()
    {
        $container      = $this->application->getContainer();
        $access_control = $container->get(__NAMESPACE__ . '\\AccessControl');

        return array(
            'onControllerLoaded' => array($access_control),
        );
    }
}
