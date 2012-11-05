<?php

namespace Town\Controller;

use Zend\View\Model\ViewModel;

class IronMeltingWorksController extends BuildingController {
	protected $buildingType = 'iron melting works';
	
	public function indexAction() {
		// Get the number of logs and lumber in the players inventory
		$nrOfIronOreInInventory = $this->getInventoryModel()->getNumberOfSpecificItemInPlayerInventory(1, 'iron ore');
		$nrOfIronInInventory = $this->getInventoryModel()->getNumberOfSpecificItemInPlayerInventory(1, 'iron');
		
		return array(
			'nrOfIronOre' => $nrOfIronOreInInventory,
			'nrOfIron' => $nrOfIronInInventory
		);
	}
	
	public function meltAction() {
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
		
		$message = 'Du smeltet et stykke jernmalm til jern.';
		
		$viewModel = new ViewModel($this->indexAction());
		$viewModel->setVariable('message', $message);
		$viewModel->setTemplate('town\iron-melting-works\index');
		
		return $viewModel;
	}
}

?>