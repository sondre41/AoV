<?php

namespace Game\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class GameController extends AbstractActionController {
	protected $inventoryModel;
	protected $inventoryTable;
	protected $itemTable;
	protected $mapTable;
	protected $playerModel;
	protected $playerTable;
	protected $townBuildingTable;
	protected $townInventoryModel;
	protected $townTable;
	
	protected function getInventoryModel() {
		if (!$this->inventoryModel) {
			$serviceManager = $this->getServiceLocator();
			$this->inventoryModel = $serviceManager->get('Game\Model\InventoryModel');
		}
		return $this->inventoryModel;
	}
	
	protected function getInventoryTable() {
		if (!$this->inventoryTable) {
			$serviceManager = $this->getServiceLocator();
			$this->inventoryTable = $serviceManager->get('Game\Model\InventoryTable');
		}
		return $this->inventoryTable;
	}
	
	protected function getItemTable() {
		if (!$this->itemTable) {
			$serviceManager = $this->getServiceLocator();
			$this->itemTable = $serviceManager->get('Game\Model\ItemTable');
		}
		return $this->itemTable;
	}
	
	protected function getMapTable() {
		if (!$this->mapTable) {
			$serviceManager = $this->getServiceLocator();
			$this->mapTable = $serviceManager->get('Game\Model\MapTable');
		}
		return $this->mapTable;
	}

	protected function getPlayerModel() {
		if (!$this->playerModel) {
			$serviceManager = $this->getServiceLocator();
			$this->playerModel = $serviceManager->get('Game\Model\PlayerModel');
		}
		return $this->playerModel;
	}
	
	protected function getPlayerTable() {
		if (!$this->playerTable) {
			$serviceManager = $this->getServiceLocator();
			$this->playerTable = $serviceManager->get('Game\Model\PlayerTable');
		}
		return $this->playerTable;
	}
	
	protected function getTownBuildingTable() {
		if (!$this->townBuildingTable) {
			$serviceManager = $this->getServiceLocator();
			$this->townBuildingTable = $serviceManager->get('Town\Model\TownBuildingTable');
		}
		return $this->townBuildingTable;
	}
	
	protected function getTownInventoryModel() {
		if (!$this->townInventoryModel) {
			$serviceManager = $this->getServiceLocator();
			$this->townInventoryModel = $serviceManager->get('Town\Model\TownInventoryModel');
		}
		return $this->townInventoryModel;
	}
	
	protected function getTownTable() {
		if (!$this->townTable) {
			$serviceManager = $this->getServiceLocator();
			$this->townTable = $serviceManager->get('Town\Model\TownTable');
		}
		return $this->townTable;
	}
}

?>