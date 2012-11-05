<?php

namespace Game;

use Game\Model\CityTable;
use Game\Model\InventoryModel;
use Game\Model\InventoryTable;
use Game\Model\ItemTable;
use Game\Model\MapTable;
use Game\Model\PlayerModel;
use Game\Model\PlayerTable;

// Zend
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
// 	public function init(ModuleManager $moduleManager)
// 	{
// 		$sharedEvents = $moduleManager->getEventManager()->getSharedManager();
// 		$sharedEvents->attach('__NAMESPACE__', '*', function($e) {
// 			// This event will only be fired when an ActionController under the MyModule namespace is dispatched.
// 			echo "Init dispatch event<br>";
// 		}, -100);
// 	}
	
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
    			'Game\Model\MapTable' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				
    				return new MapTable($databaseAdapter);
    			},
    			'Game\Model\PlayerTable' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				
    				return new PlayerTable($databaseAdapter);
    			},
    			'Game\Model\ItemTable' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				
    				return new ItemTable($databaseAdapter);
    			},
    			'Game\Model\InventoryTable' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				
    				return new InventoryTable($databaseAdapter);
    			},
    			'Game\Model\InventoryModel' => function($serviceManager) {
	    			$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
	    			
	    			return new InventoryModel($databaseAdapter, $serviceManager);
    			},
    			'Game\Model\CityTable' => function($serviceManager) {
	    			$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
	    			
	    			return new CityTable($databaseAdapter);
    			}
    		)
    	);
    }
    
    public function getControllerConfig() {
    	return array(
    		'factories' => array(
    			'Game\Controller\Mapsquare' => function($controllerManager) {
	    			$serviceManager = $controllerManager->getServiceLocator();
	    			$eventManager = $serviceManager->get('EventManager');
	    			$controller = new Controller\MapsquareController();
	    			
	    			echo "WTFFFFFF!";
	    			
	    			$eventManager->attach('dispatch', function ($e) use ($controller) {
	    				echo "hei";
	    			}, 100); // execute before executing action logic
	    			
	    			echo "shallabais";
	    			
	    			$controller->setEventManager($eventManager);
	    			
	    			return $controller;
    			}
    		)
    	);
    }
}

?>