<?php

namespace Game\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class InventoryController extends AbstractActionController {
	protected $playerTable;
	protected $inventoryTable;
	protected $inventoryModel;
	
	public function indexAction() {
		return array('inventoryItems' => $this->getInventoryModel()->getPlayerInventory(1));
	}
	
	private function getPlayerTable() {
		if (!$this->playerTable) {
			$serviceManager = $this->getServiceLocator();
			$this->playerTable = $serviceManager->get('Game\Models\PlayerTable');
		}
		return $this->playerTable;
	}
	
	private function getInventoryTable() {
		if (!$this->inventoryTable) {
			$serviceManager = $this->getServiceLocator();
			$this->inventoryTable = $serviceManager->get('Game\Models\InventoryTable');
		}
		return $this->inventoryTable;
	}
	
	private function getInventoryModel() {
		if (!$this->inventoryModel) {
			$serviceManager = $this->getServiceLocator();
			$this->inventoryModel = $serviceManager->get('Game\Models\Inventory');
		}
		return $this->inventoryModel;
	}
}

?>