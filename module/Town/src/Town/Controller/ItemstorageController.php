<?php

namespace Town\Controller;

use Zend\View\Model\ViewModel;

class ItemstorageController extends BuildingController {
	protected $buildingType = 'item storage';
	
	public function indexAction() {
		// Get the players inventory
		$playerInventory = $this->getInventoryModel()->getPlayerInventory(1, false);
		
		// Get the players inventory in the town
		$playerTownInventory = $this->getTownInventoryModel()->getPlayerTownInventory(1, $this->town->townID);
		
		return array(
			'playerInventory' => $playerInventory,
			'playerTownInventory' => $playerTownInventory
		);
	}
	
	public function depositAction() {
		// Get an eventual inventory ID from the request
		$inventoryID = $this->getRequest()->getPost()->inventoryID;
		if(!$inventoryID) {
			// No item chosen, redirect to town inventory index
			return $this->redirect()->toRoute('town', array(
					'controller' => 'itemstorage',
					'longitude' => $this->longitude,
					'latitude' => $this->latitude
			));
		} else {
			// Instantiate the view model
			$view = new ViewModel();
			
			// Get the inventory item
			$inventoryItem = $this->getInventoryModel()->getInventoryItem($inventoryID);
			if($inventoryItem) {
				// Delete the item from the players inventory
				$nrOfItemsDeleted = $this->getInventoryTable()->deleteItemForPlayer($inventoryID, 1);
				
				// And add it to the players town inventory
				$this->getTownInventoryModel()->insert($inventoryItem->itemID, 1, $this->town->townID);
	
				// Give a message to the user about the successful resource deposit
				$message = "Gjenstanden ble lagt i landsbyens lager.";
				$view->setVariable('message', $message);
			}
			
			$view->setVariables($this->indexAction());
		}
	
		// Re-render inventory
		$view->setTemplate('town/itemstorage/index');
		return $view;
	}
	
	public function recoverAction() {
		// Get an eventual town inventory ID from the request
		$townInventoryID = $this->getRequest()->getPost()->townInventoryID;
		if(!$townInventoryID) {
			// No item chosen, redirect to town inventory index
			return $this->redirect()->toRoute('town', array(
					'controller' => 'itemstorage',
					'longitude' => $this->longitude,
					'latitude' => $this->latitude
			));
		} else {
			// Instantiate the view model
			$view = new ViewModel();
			
			// Get the town inventory item
			$townInventoryItem = $this->getTownInventoryModel()->getTownInventoryItem($townInventoryID);
			
			if($townInventoryItem) {
				// Delete the item from the players town inventory
				$nrOfItemsDeleted = $this->getTownInventoryModel()->delete($townInventoryID);
	
				// And add it to the players inventory
				$this->getInventoryTable()->giveItemToPlayer($townInventoryItem->itemID, 1);
	
				// Give a message to the user about the successful resource deposit
				$message = "Gjenstanden ble hentet til din inventar.";
				$view->setVariable('message', $message);
			}
			
			$view->setVariables($this->indexAction());
		}
	
		// Re-render inventory
		$view->setTemplate('town/itemstorage/index');
		return $view;
	}
}

?>