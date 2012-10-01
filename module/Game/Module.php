<?php

namespace Game;

use Game\Models\Inventory;
use Game\Models\InventoryTable;
use Game\Models\ItemTable;
use Game\Models\MapTable;
use Game\Models\PlayerTable;
use Zend\Mvc\ModuleRouteListener;

class Module
{
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
    			'Game\Models\MapTable' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				
    				return new MapTable($databaseAdapter);
    			},
    			'Game\Models\PlayerTable' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				
    				return new PlayerTable($databaseAdapter);
    			},
    			'Game\Models\ItemTable' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				
    				return new ItemTable($databaseAdapter);
    			},
    			'Game\Models\InventoryTable' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				
    				return new InventoryTable($databaseAdapter);
    			},
    			'Game\Models\Inventory' => function($serviceManager) {
	    			$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
	    			
	    			return new Inventory($databaseAdapter);
    			}
    		)
    	);
    }
}

?>