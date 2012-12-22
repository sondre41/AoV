<?php

namespace Game;

use Game\Model\CityTable;
use Game\Model\FortModel;
use Game\Model\GuildModel;
use Game\Model\InventoryModel;
use Game\Model\InventoryTable;
use Game\Model\ItemTable;
use Game\Model\MapTable;
use Game\Model\MessageModel;
use Game\Model\PlayerModel;
use Game\Model\PlayerTable;
use Game\Model\QuestModel;

// Zend
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module {
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
    			// City Table
    			'Game\Model\CityTable' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				
    				return new CityTable($databaseAdapter);
    			},
    			
    			// Fort Model
    			'Game\Model\FortModel' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				
    				$fortTableGateway = new TableGateway('fort', $databaseAdapter);
    				$fortFightTableGateway = new TableGateway('fortfight', $databaseAdapter);
    				$fortFightInvitationTableGateway = new TableGateway('fortfightinvitation', $databaseAdapter);
    			
    				return new FortModel($databaseAdapter, $fortTableGateway, $fortFightTableGateway,  $fortFightInvitationTableGateway);
    			},
    				
    			// Guild Model
    			'Game\Model\GuildModel' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				
    				$guildInvitationTableGateway = new TableGateway('guildinvitation', $databaseAdapter);
    				$guildTableGateway = new TableGateway('guild', $databaseAdapter);
    				
    				return new GuildModel($databaseAdapter, $guildInvitationTableGateway, $guildTableGateway);
    			},
    			
    			// Inventory Model
    			'Game\Model\InventoryModel' => function($serviceManager) {
	    			$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
	    			
	    			return new InventoryModel($databaseAdapter, $serviceManager);
    			},
    			
    			// Inventory Table
    			'Game\Model\InventoryTable' => function($serviceManager) {
	    			$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
	    			
	    			return new InventoryTable($databaseAdapter);
    			},
    			
    			// Item Table
	    			'Game\Model\ItemTable' => function($serviceManager) {
	    			$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
	    			
	    			return new ItemTable($databaseAdapter);
    			},
    			
    			// Map Table
    			'Game\Model\MapTable' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				
    				return new MapTable($databaseAdapter);
    			},
    			
    			// Message Model
    			'Game\Model\MessageModel' => function($serviceManager) {
	    			$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
	    			$messageTableGateway = new TableGateway('message', $databaseAdapter);
	    			
	    			return new MessageModel($databaseAdapter, $messageTableGateway);
    			},
    			
    			// Player Table
    			'Game\Model\PlayerTable' => function($serviceManager) {
    				$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
    				
    				return new PlayerTable($databaseAdapter);
    			},
    			    			
    			// Quest Model
    			'Game\Model\QuestModel' => function($serviceManager) {
	    			$databaseAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
	    			$questTableGateway = new TableGateway('quest', $databaseAdapter);
	    			$playerQuestTableGateway = new TableGateway('playerquest', $databaseAdapter);
	    				
	    			return new QuestModel($databaseAdapter, $questTableGateway, $playerQuestTableGateway, $serviceManager);
    			}
    		)
    	);
    }
}

?>