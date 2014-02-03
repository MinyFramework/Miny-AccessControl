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
        $factory = $app->getFactory();

        $factory->add('roles', '\Modules\AccessControl\RoleContainer')
            ->addMethodCall('addRoles', '@access_control:roles');

        $factory->add('access_control', '\Modules\AccessControl\AccessControl')
            ->addMethodCall('setRoleContainer', '&roles')
            ->addMethodCall('setAnnotation', '&annotation')
            ->addMethodCall('setRouteGenerator', '&route_generator')
            ->addMethodCall('setApplication', $app)
            ->addMethodCall('setLog', '&log');
    }

    public function eventHandlers()
    {
        $factory  = $this->application->getFactory();
        $access_control = $factory->get('access_control');

        return array(
            'onControllerLoaded' => array($access_control),
        );
    }
}
