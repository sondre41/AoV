<?php

namespace Town\Controller;

use Zend\View\Model\ViewModel;

class IronMeltingWorksController extends BuildingController {
	protected $buildingType = 'iron melting works';
	
	public function indexAction() {
		// Get the number of logs and lumber in the players inventory
		$nrOfIronOreInInventory = $this->getInventoryModel()->getNumberOfSpecificItemInPlayerInventory(1, 'iron ore');
		$nrOfIronInInventory = $this->getInventoryModel()->getNumberOfSpecificItemInPlayerInventory(1, 'iron');
		
		// Find out if this town has a Resource Storage built
		$townHasStorage = $this->getTownBuildingTable()->townHasBuilding('resource storage', $this->town->townID);
		
		return array(
			'townHasStorage' => $townHasStorage,
			'townNrOfIronOre' => $this->town->ironOre,
			'townNrOfIron' => $this->town->iron,
			'nrOfIronOre' => $nrOfIronOreInInventory,
			'nrOfIron' => $nrOfIronInInventory
		);
	}
	
	public function meltAction() {
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
			// Check that the town actually has any iron ore available
			if($this->town->ironOre <= 0) {
				return $this->redirect()->toRoute('town', array(
						'controller' => 'index',
						'longitude' => $this->longitude,
						'latitude' => $this->latitude
				));
			}
				
			// Decrement the number of iron ore and increment the number of iron available in the town
			$this->getTownTable()->reduceAvailableResources('ironOre', $this->town->townID);
			$this->getTownTable()->incrementAvailableResources('iron', $this->town->townID);
				
			// Update town information
			$this->reloadTown();
		} else {
			// Try to delete a log item from the players inventory
			$itemDeleted = $this->getInventoryModel()->deleteItemOfNameFromPlayerInventory(1, 'iron ore');
			if(!$itemDeleted) {
				// There was no log item in the players inventory. Redirect to index.
				return $this->redirect()->toRoute('town', array(
						'controller' => 'iron-melting-works',
						'longitude' => $this->longitude,
						'latitude' => $this->latitude
				));
			}
			
			// Give the player a lumber item
			$ironItem = $this->getItemTable()->getItem('iron');
			$this->getInventoryTable()->giveItemToPlayer($ironItem->itemID, 1);
		}
			
		$message = 'Du smeltet et stykke jernmalm til jern.';
		
		$viewModel = new ViewModel($this->indexAction());
		$viewModel->setVariable('message', $message);
		$viewModel->setTemplate('town\iron-melting-works\index');
		
		return $viewModel;
	}
}

?>