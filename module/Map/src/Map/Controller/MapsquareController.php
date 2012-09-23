<?php

namespace Map\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MapsquareController extends AbstractActionController {
	protected $mapTable;
	protected $playerTable;
	protected $itemTable;
	protected $inventoryTable;
	
	public function indexAction() {
		// Get the type of the map square the player is currently on
		$player = $this->getPlayerTable()->getPlayer(1);
		$mapSquare = $this->getMapTable()->getMapSquare($player->longitude, $player->latitude);
		$squareType = $mapSquare->type;
		
		switch($squareType) {
			case 'forest':
				return $this->forest();
		}
		
		return new ViewModel(array(
			'mapSquare' => $mapSquare
		));
	}
	
	public function forest() {
		$player = $this->getPlayerTable()->getPlayer(1);
		if($player->actionsBlockedTime() == 0) {
			$request = $this->getRequest();
			if($request->isPost()) {
				$squareAction = $request->getPost('squareAction');
				if($squareAction == 'forest') {
					// Give the player a lumber item
					$lumberItem = $this->getItemTable()->getItem('lumber');
					$this->getInventoryTable()->giveItemToPlayer($lumberItem->itemID, 1);
						
					// Block actions for the player for 3 minutes
					$secondsToBlock = 60 * 3;
					$this->getPlayerTable()->setPlayerActionsBlocked($secondsToBlock);
				}
			} else {
				// Player is able to cut more trees, display form/button
				$view = new ViewModel(array('blocked' => false));
				$view->setTemplate('map/mapsquare/forest');
				return $view;
			}
		}
		// Display time left counter
		$view = new ViewModel(array('blocked' => true));
		$view->setTemplate('map/mapsquare/forest');
		return $view;
	}
	
	private function getMapTable() {
		if (!$this->mapTable) {
			$serviceManager = $this->getServiceLocator();
			$this->mapTable = $serviceManager->get('Map\Models\MapTable');
		}
		return $this->mapTable;
	}
	
	private function getPlayerTable() {
		if (!$this->playerTable) {
			$serviceManager = $this->getServiceLocator();
			$this->playerTable = $serviceManager->get('Map\Models\PlayerTable');
		}
		return $this->playerTable;
	}
	
	private function getItemTable() {
		if (!$this->itemTable) {
			$serviceManager = $this->getServiceLocator();
			$this->itemTable = $serviceManager->get('Map\Models\ItemTable');
		}
		return $this->itemTable;
	}
	
	private function getInventoryTable() {
		if (!$this->inventoryTable) {
			$serviceManager = $this->getServiceLocator();
			$this->inventoryTable = $serviceManager->get('Map\Models\InventoryTable');
		}
		return $this->inventoryTable;
	}
}

?>