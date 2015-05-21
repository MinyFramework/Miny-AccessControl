<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <bugadani@gmail.com>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\AccessControl;

use Miny\Application\BaseApplication;
use Miny\CoreEvents;

class Module extends \Miny\Modules\Module
{
    public function getDependencies()
    {
        return array('Annotation');
    }

    public function defaultConfiguration()
    {
        return array(
            'authorizationHandler' => '\\Modules\\AccessControl\\RequestHandler',
            'roles' => array(),
            'redirect_parameters' => array()
        );
    }

    public function init(BaseApplication $app)
    {
        $container = $app->getContainer();

        $container->addAlias(
            '\\Modules\\AccessControl\\RequestHandlerInterface',
            $this->getConfiguration('authorizationHandler')
        );

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
                if (!$module->hasConfiguration('redirect_route')) {
                    return;
                }
                $handler->setDefaultRedirection(
                    $module->getConfiguration('redirect_route'),
                    $module->getConfiguration('redirect_parameters')
                );
            }
        );
    }

    public function eventHandlers()
    {
        $container = $this->application->getContainer();

        return array(
            CoreEvents::CONTROLLER_LOADED => array(
                $container->get(
                    __NAMESPACE__ . '\\AccessControl'
                )
            ),
        );
    }
}
