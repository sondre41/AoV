<?php

namespace Game;

use Game\Models\CityTable;
use Game\Models\InventoryModel;
use Game\Models\InventoryTable;
use Game\Models\ItemTable;
use Game\Models\MapTable;
use Game\Models\PlayerModel;
use Game\Models\PlayerTable;

// Zend
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

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
    			'Game\Models\InventoryModel' => function($serviceManager) {
	    			$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
	    			
	    			return new InventoryModel($databaseAdapter);
    			},
    			'Game\Models\CityTable' => function($serviceManager) {
	    			$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
	    			
	    			return new CityTable($databaseAdapter);
    			}
    		)
    	);
    }
    
    public function getControllerConfig() {
    	return array(
    		'factories' => array(
    			'Game\Controller\Mapsquare' => function($controllers) {
	    			$serviceManager = $controllers->getServiceLocator();
	    			$eventManager = $serviceManager->get('EventManager');
	    			$controller = new Controller\MapsquareController();
	    			
	    			echo "WTFFFFFF!";
	    			
	    			$eventManager->attach('*', function ($e) use ($controller) {
	    				echo "hei";
	    				$request = $e->getRequest();
	    				$method  = $request->getMethod();
	    				if (!in_array($method, array('PUT', 'DELETE', 'PATCH'))) {
	    					// nothing to do
	    					return;
	    				}
	    			
	    				if ($controller->params()->fromRoute('id', false)) {
	    					// nothing to do
	    					return;
	    				}
	    			
	    				// Missing identifier! Redirect.
	    				return $controller->redirect()->toRoute(/* ... */);
	    			}, 100); // execute before executing action logic
	    			
// 	    			$eventManager->attach(MvcEvent::EVENT_DISPATCH, function($mvcEvent) use($controller) {
// 	    				echo "<br>WTFFFFFFFFFFFFFFFF!!!!!";
// 	    				// Get longitude and latitude from request
// 	    				$longitude = $controller->params()->fromRoute('longitude', false);
// 	    				$latitude = $controller->params()->fromRoute('latitude', false);
	    					
// 	    				if(!$longitude || !$latitude) {
// 	    					// Redirect to map controller
// 	    					return $controller->redirect()->toRoute('game', array(
// 	    							'controller' => 'map'
// 	    					));
// 	    				}
	    				
// 	    				// Check if mapsquare is in range of player
// 	    				$player = $controller->getPlayerTable()->getPlayer(1);
	    				
// 	    				$longitudeDiff = abs($longitude - $player->longitude);
// 	    				$latitudeDiff = abs($latitude - $player->latitude);
// 	    				if($longitudeDiff > 4 || $latitudeDiff > 4) {
// 	    					// Redirect to map controller
// 	    					return $controller->redirect()->toRoute('game', array(
// 	    							'controller' => 'map'
// 	    					));
// 	    				}
// 	    			}, 100); // Execute before controller's action logic
	    			
	    			$controller->setEventManager($eventManager);
	    			
	    			return $controller;
    			}
    		)
    	);
    }
}

?>