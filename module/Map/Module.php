<?php

namespace Map;

use Zend\Mvc\ModuleRouteListener;
use Map\Models\MapTable;

class Module
{
    public function onBootstrap($e)
    {
        $e->getApplication()->getServiceManager()->get('translator');
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
            ),
        );
    }
    
    public function getServiceConfig() {
    	return array(
    		'factories' => array(
    			'Map\Models\MapTable' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				$mapTableModel = new MapTable($databaseAdapter);
    				
    				return $mapTableModel;
    			}
    		)
    	);
    }
}

?>