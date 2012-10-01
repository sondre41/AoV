<?php

namespace Application;

use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\Mvc\ModuleRouteListener;

class Module
{
    public function onBootstrap($e)
    {
    	// Set up translator
        //$e->getApplication()->getServiceManager()->get('translator');
        
    	// Set up route module listener
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            )
        );
    }
}

?>