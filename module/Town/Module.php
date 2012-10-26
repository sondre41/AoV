<?php

namespace Town;

use Town\Model\TownTable;
use Town\Model\TownBuildingTable;

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
    			'Town\Model\TownTable' => function($serviceManager) {
	    			$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
	    			
	    			return new TownTable($databaseAdapter, $serviceManager);
    			},
    			'Town\Model\TownBuildingTable' => function($serviceManager) {
	    			$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
	    			
	    			return new TownBuildingTable($databaseAdapter);
    			}
    		)
    	);
    }
}

?>