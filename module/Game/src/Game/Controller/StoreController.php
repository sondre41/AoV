<?php

namespace Game\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class StoreController extends AbstractActionController {
	protected $inventoryTable;
	protected $itemTable;
	
	public function indexAction() {
		
	}
	
	private function getInventoryTable() {
		if (!$this->inventoryTable) {
			$serviceManager = $this->getServiceLocator();
			$this->inventoryTable = $serviceManager->get('Game\Models\InventoryTable');
		}
		return $this->inventoryTable;
	}
	
	private function getItemTable() {
		if (!$this->itemTable) {
			$serviceManager = $this->getServiceLocator();
			$this->itemTable = $serviceManager->get('Game\Models\ItemTable');
		}
		return $this->itemTable;
	}
}

?>