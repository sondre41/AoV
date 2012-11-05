<?php

namespace Town\Controller;

use Zend\View\Model\ViewModel;

class SawBuckController extends BuildingController {
	protected $buildingType = 'saw buck';
	
	public function indexAction() {
		// Get the number of logs and lumber in the players inventory
		$nrOfLogInInventory = $this->getInventoryModel()->getNumberOfSpecificItemInPlayerInventory(1, 'log');
		$nrOfLumberInInventory = $this->getInventoryModel()->getNumberOfSpecificItemInPlayerInventory(1, 'lumber');
		
		return array(
			'nrOfLog' => $nrOfLogInInventory,
			'nrOfLumber' => $nrOfLumberInInventory
		);
	}
	
	public function cutAction() {
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
		
		$message = 'Du sagde en tømmerstokk til plank.';
		
		$viewModel = new ViewModel($this->indexAction());
		$viewModel->setVariable('message', $message);
		$viewModel->setTemplate('town\saw-buck\index');
		
		return $viewModel;
	}
}

?>