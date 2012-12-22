<?php

namespace Town\Controller;

use Zend\View\Model\ViewModel;

class SawBuckController extends BuildingController {
	protected $buildingType = 'saw buck';
	
	public function indexAction() {
		// Get the number of logs and lumber in the players inventory
		$nrOfLogInInventory = $this->getInventoryModel()->getNumberOfSpecificItemInPlayerInventory(1, 'log');
		$nrOfLumberInInventory = $this->getInventoryModel()->getNumberOfSpecificItemInPlayerInventory(1, 'lumber');
		
		// Find out if this town has a Resource Storage built
		$townHasStorage = $this->getTownBuildingTable()->townHasBuilding('resource storage', $this->town->townID);
		
		return array(
			'townHasStorage' => $townHasStorage,
			'townNrOfLog' => $this->town->log,
			'townNrOfLumber' => $this->town->lumber,
			'nrOfLog' => $nrOfLogInInventory,
			'nrOfLumber' => $nrOfLumberInInventory
		);
	}
	
	public function cutAction() {
		$request = $this->getRequest();
		if(!$request->isPost()) {
			// Request must be POST
			return $this->redirect()->toRoute('town', array(
					'controller' => 'saw-buck',
					'longitude' => $this->longitude,
					'latitude' => $this->latitude
			));
		}
		
		$resourceContainer = $request->getPost()->resourceContainer;
		if($resourceContainer != 'storage' && $resourceContainer != 'inventory') {
			// The user has been messing with the request. Report.
			return $this->redirect()->toRoute('town', array(
					'controller' => 'index',
					'longitude' => $this->longitude,
					'latitude' => $this->latitude
			));
		}
		
		if($resourceContainer == 'storage') {
			// Check that the town actually has a log available
			if($this->town->log <= 0) {
				return $this->redirect()->toRoute('town', array(
						'controller' => 'index',
						'longitude' => $this->longitude,
						'latitude' => $this->latitude
				));
			}
			
			// Decrement the number of logs and increment the number of lumber available in the town
			$this->getTownTable()->reduceAvailableResources('log', $this->town->townID);
			$this->getTownTable()->incrementAvailableResources('lumber', $this->town->townID);
			
			// Update town information
			$this->reloadTown();
		} else {
			// Try to delete a log item from the players inventory
			$itemDeleted = $this->getInventoryModel()->deleteItemOfNameFromPlayerInventory(1, 'log');
			if(!$itemDeleted) {
				// There was no log item in the players inventory. Redirect to index.
				return $this->redirect()->toRoute('town', array(
						'controller' => 'saw-buck',
						'longitude' => $this->longitude,
						'latitude' => $this->latitude
				));
			}
			
			// Give the player a lumber item
			$lumberItem = $this->getItemTable()->getItem('lumber');
			$this->getInventoryTable()->giveItemToPlayer($lumberItem->itemID, 1);
		}
		
		$message = 'Du sagde en tømmerstokk til plank.';
		
		$viewModel = new ViewModel($this->indexAction());
		$viewModel->setVariable('message', $message);
		$viewModel->setTemplate('town\saw-buck\index');
		
		return $viewModel;
	}
}

?>