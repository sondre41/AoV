<?php

namespace Town\Controller;

use Zend\View\Model\ViewModel;

class ResourceStorageController extends BuildingController {
	protected $buildingType = 'resource storage';
	
	public function indexAction() {
		// Get the resource names/types that can be deposited in the towns resource storage from the players inventory
		$resourceNames = array(
			'log',
			'stone',
			'lumber'
		);
		$inventoryItems = $this->getInventoryModel()->getPlayerInventory(1, false, null, null, $resourceNames)->toArray();
		
		return array(
			'town' => $this->town,
			'inventoryItems' => $inventoryItems
		);
	}
	
	public function depositAction() {
		// Get an eventual inventory ID from the request
		$inventoryID = $this->getRequest()->getPost()->inventoryID;
		if(!$inventoryID) {
			// No item chosen. Redirect to inventory index.
			return $this->redirect()->toRoute('town', array(
					'controller' => 'resource-storage',
					'longitude' => $this->longitude,
					'latitude' => $this->latitude
			));
		} else {
			// Instantiate the view model
			$view = new ViewModel();
			
			// Get the inventory item
			$inventoryItem = $this->getInventoryModel()->getInventoryItem($inventoryID);
			if($inventoryItem) {
				// Delete the item
				$nrOfItemsDeleted = $this->getInventoryTable()->deleteItemForPlayer($inventoryID, 1);
				
				// Increment the number of available resources in the town of the type of the given resource
				$this->getTownTable()->incrementAvailableResources($inventoryItem->name, $this->town->townID);
				
				// Give a message to the user about the successful resource deposit
				$message = "Resursen ble lagt i landsbyens lager.";
				$view->setVariable('message', $message);
			}
			
			// Update the town resource counts
			$this->reloadTown();
			$view->setVariables($this->indexAction());
		}
		
		// Re-render inventory
		$view->setTemplate('town/resource-storage/index');
		return $view;
	}
}

?>