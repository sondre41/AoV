<?php

namespace Map;

use Map\Models\InventoryTable;

use Map\Models\ItemTable;

use Zend\Mvc\ModuleRouteListener;
use Map\Models\MapTable;
use Map\Models\PlayerTable;

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
    				
    				return new MapTable($databaseAdapter);
    			},
    			'Map\Models\PlayerTable' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				
    				return new PlayerTable($databaseAdapter);
    			},
    			'Map\Models\ItemTable' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				
    				return new ItemTable($databaseAdapter);
    			},
    			'Map\Models\InventoryTable' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				
    				return new InventoryTable($databaseAdapter);
    			}
    		)
    	);
    }
}

?>