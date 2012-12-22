<?php

namespace Town;

use Town\Model\RecipeModel;
use Town\Model\TownBuildingTable;
use Town\Model\TownInventoryModel;
use Town\Model\TownTable;

// Zend
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Zend\Mvc\Controller\ControllerManager;

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
    			// Models
    			// RecipeModel
    			'Town\Model\RecipeModel' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				$recipeTableGateway = new TableGateway('recipe', $databaseAdapter);
    				$playerRecipeTableGateway = new TableGateway('playerrecipe', $databaseAdapter);
    				 
    				return new RecipeModel($databaseAdapter, $recipeTableGateway, $playerRecipeTableGateway);
    			},
    			
    			// TownBuildingTable
    			'Town\Model\TownBuildingTable' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				
    				return new TownBuildingTable($databaseAdapter);
    			},
    			
    			// TownInventoryModel
    			'Town\Model\TownInventoryModel' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				$tableGateway = new TableGateway('towninventory', $databaseAdapter);
    				
    				return new TownInventoryModel($databaseAdapter, $tableGateway);
    			},
    			
    			// TownTable
    			'Town\Model\TownTable' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
	    			$tableGateway = new TableGateway('town', $databaseAdapter);
    				
	    			return new TownTable($tableGateway, $serviceManager);
    			}
    		)
    	);
    }
}

?>