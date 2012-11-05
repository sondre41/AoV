<?php

namespace Game\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MapsquareController extends AbstractActionController {
	protected $mapTable;
	protected $playerTable;
	protected $itemTable;
	protected $inventoryTable;
	protected $inventoryModel;
	
	public function indexAction() {
		// Get the type of the map square the player is currently on
		$player = $this->getPlayerTable()->getPlayer(1);
		$mapSquare = $this->getMapTable()->getMapSquare($player->longitude, $player->latitude);
		$squareType = $mapSquare->type;
		
		switch($squareType) {
			case 'forest':
				return $this->forest($player);
		}
		
		return new ViewModel(array(
			'mapSquare' => $mapSquare
		));
	}
	
	public function forest($player) {
		if($player->actionsBlockedTime() == 0) {
			// Check if the player has an axe
			if($this->getInventoryModel()->playerHasItemTypeActive(1, 'axe')) {
				$request = $this->getRequest();
				if($request->isPost()) {
					$squareAction = $request->getPost('squareAction');
					if($squareAction == 'forest') {
						// Give the player a lumber item
						$lumberItem = $this->getItemTable()->getItem('logs');
						$this->getInventoryTable()->giveItemToPlayer($lumberItem->itemID, 1);
						
						// Block actions for the player for 3 minutes
						$secondsToBlock = 60 * 3;
						$this->getPlayerTable()->setPlayerActionsBlocked($secondsToBlock);
						
						// Give message to player
						$message = "Du har nå påbegynt hugging av et tre. Det tar 3 minutter før du er ferdig.";
					}
				} else {
					// Player is able to cut more trees, display form/button
					$view = new ViewModel(array('blocked' => false));
					$view->setTemplate('game/mapsquare/forest');
					return $view;
				}
			} else {
				// Player does not have an axe active, give message
				$message = "Du må ha en øks aktiv i din inventar for å kunne hugge et tre.";
			}
		} else {
			// Display time left counter
			$message = "Du er i ferd med å hugge et tre.";
		}
		
		$view = new ViewModel(array('blocked' => true, 'message' => $message));
		$view->setTemplate('game/mapsquare/forest');
		return $view;
	}
	
	private function getMapTable() {
		if (!$this->mapTable) {
			$serviceManager = $this->getServiceLocator();
			$this->mapTable = $serviceManager->get('Game\Model\MapTable');
		}
		return $this->mapTable;
	}
	
	private function getPlayerTable() {
		if (!$this->playerTable) {
			$serviceManager = $this->getServiceLocator();
			$this->playerTable = $serviceManager->get('Game\Model\PlayerTable');
		}
		return $this->playerTable;
	}
	
	private function getItemTable() {
		if (!$this->itemTable) {
			$serviceManager = $this->getServiceLocator();
			$this->itemTable = $serviceManager->get('Game\Model\ItemTable');
		}
		return $this->itemTable;
	}
	
	private function getInventoryTable() {
		if (!$this->inventoryTable) {
			$serviceManager = $this->getServiceLocator();
			$this->inventoryTable = $serviceManager->get('Game\Model\InventoryTable');
		}
		return $this->inventoryTable;
	}
	
	private function getInventoryModel() {
		if (!$this->inventoryModel) {
			$serviceManager = $this->getServiceLocator();
			$this->inventoryModel = $serviceManager->get('Game\Model\InventoryModel');
		}
		return $this->inventoryModel;
	}
}

?>