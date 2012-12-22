<?php

namespace Game\Controller;

use Zend\Authentication\AuthenticationService;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;

class GameController extends AbstractActionController {
	protected $player;
	protected $playerId;
	
	// Models
	protected $fortModel;
	protected $guildModel;
	protected $inventoryModel;
	protected $inventoryTable;
	protected $itemTable;
	protected $mapTable;
	protected $messageModel;
	protected $playerTable;
	protected $questModel;
	protected $recipeModel;
	protected $townBuildingTable;
	protected $townInventoryModel;
	protected $townTable;
	
	public function setEventManager(EventManagerInterface $eventManager)
	{
		parent::setEventManager($eventManager);
			
		// Attach init method to be run before controller action
		$eventManager->attach('dispatch', array($this, 'init'), 100);
	}
	
	public function init() {
		// Check that the user is logged in
		$auth = new AuthenticationService();
		
		if(! $auth->hasIdentity()) {
			// Redirect to login form
			$this->redirect()->toRoute('application', array(
				'controller' => 'auth'
			));
		}
		
		$this->player = $auth->getStorage()->read();
		$this->playerId = $this->player->playerID;
	}
	
	
	protected function getFortModel() {
		if (! $this->fortModel) {
			$serviceManager = $this->getServiceLocator();
			$this->fortModel = $serviceManager->get('Game\Model\FortModel');
		}
		return $this->fortModel;
	}
	
	protected function getGuildModel() {
		if (! $this->guildModel) {
			$serviceManager = $this->getServiceLocator();
			$this->guildModel = $serviceManager->get('Game\Model\GuildModel');
		}
		return $this->guildModel;
	}
	
	protected function getInventoryModel() {
		if (! $this->inventoryModel) {
			$serviceManager = $this->getServiceLocator();
			$this->inventoryModel = $serviceManager->get('Game\Model\InventoryModel');
		}
		return $this->inventoryModel;
	}
	
	protected function getInventoryTable() {
		if (! $this->inventoryTable) {
			$serviceManager = $this->getServiceLocator();
			$this->inventoryTable = $serviceManager->get('Game\Model\InventoryTable');
		}
		return $this->inventoryTable;
	}
	
	protected function getItemTable() {
		if (! $this->itemTable) {
			$serviceManager = $this->getServiceLocator();
			$this->itemTable = $serviceManager->get('Game\Model\ItemTable');
		}
		return $this->itemTable;
	}
	
	protected function getMapTable() {
		if (! $this->mapTable) {
			$serviceManager = $this->getServiceLocator();
			$this->mapTable = $serviceManager->get('Game\Model\MapTable');
		}
		return $this->mapTable;
	}
	
	protected function getMessageModel() {
		if (! $this->messageModel) {
			$serviceManager = $this->getServiceLocator();
			$this->messageModel = $serviceManager->get('Game\Model\MessageModel');
		}
		return $this->messageModel;
	}
	
	protected function getPlayerTable() {
		if (! $this->playerTable) {
			$serviceManager = $this->getServiceLocator();
			$this->playerTable = $serviceManager->get('Game\Model\PlayerTable');
		}
		return $this->playerTable;
	}
	
	protected function getQuestModel() {
		if (! $this->questModel) {
			$serviceManager = $this->getServiceLocator();
			$this->questModel = $serviceManager->get('Game\Model\QuestModel');
		}
		return $this->questModel;
	}
	
	protected function getRecipeModel() {
		if (! $this->recipeModel) {
			$serviceManager = $this->getServiceLocator();
			$this->recipeModel = $serviceManager->get('Town\Model\RecipeModel');
		}
		return $this->recipeModel;
	}
	
	protected function getTownBuildingTable() {
		if (! $this->townBuildingTable) {
			$serviceManager = $this->getServiceLocator();
			$this->townBuildingTable = $serviceManager->get('Town\Model\TownBuildingTable');
		}
		return $this->townBuildingTable;
	}
	
	protected function getTownInventoryModel() {
		if (! $this->townInventoryModel) {
			$serviceManager = $this->getServiceLocator();
			$this->townInventoryModel = $serviceManager->get('Town\Model\TownInventoryModel');
		}
		return $this->townInventoryModel;
	}
	
	protected function getTownTable() {
		if (! $this->townTable) {
			$serviceManager = $this->getServiceLocator();
			$this->townTable = $serviceManager->get('Town\Model\TownTable');
		}
		return $this->townTable;
	}
}

?>