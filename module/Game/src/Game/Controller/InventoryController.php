<?php

namespace Game\Controller;

use Zend\View\Model\ViewModel;

class InventoryController extends GameController {
	public function indexAction() {	
		return array('inventoryItems' => $this->getInventoryModel()->getPlayerInventory($this->player->playerID)->toArray());
	}
	
	public function deleteAction() {
		$inventoryID = $this->getRequest()->getPost()->inventoryID;
		if(!$inventoryID) {
			// No item chosen. Redirect to inventory index.
			return $this->redirect()->toRoute('game', array(
					'controller' => 'inventory'
			));
		} else {
			// Delete the item
			$nrOfItemsDeleted = $this->getInventoryTable()->deleteItemForPlayer($inventoryID, $this->player->playerID);
			
			if($nrOfItemsDeleted == count($inventoryID)) {
				// Give a message to the user about the successful deletion
				$message = "Gjenstanden ble slettet fra din inventar.";
			
				// Return message to view
				$view = new ViewModel(array_merge(
					$this->indexAction(),
					array('message' => $message)
				));
			} else {
				$view = new ViewModel($this->indexAction());
			}
		}
		
		// Re-render inventory
		$view->setTemplate('game/inventory/index');
		return $view;
	}
	
	public function activateAction() {
		$request = $this->getRequest();
		if(!$request->isPost()) {
			// The user has manually gone to this page via URL. Redirect to inventory.
			return $this->redirect()->toRoute('game', array(
					'controller' => 'inventory'
			));
		} else {
			$inventoryID = $request->getPost()->inventoryID;
			$hand = $request->getPost()->hand;
			$activationSuccessful = $this->getInventoryModel()->activateItemForPlayer($inventoryID, $this->player->playerID, $hand);
			
			if(!$activationSuccessful) {
				// Suspicious behavior. The user has probably tampered with the html header.
				// To be done: Report event as suspicious behavior for this user.
				$message = "En feil oppstod under aktivering av gjenstanden.";
			} else {
				// Return message to view
				$message = "Gjenstanden ble aktivert.";
			}
			
			$view = new ViewModel(array_merge(
					$this->indexAction(),
					array('message' => $message)
			));
			
			// Re-render inventory
			$view->setTemplate('game/inventory/index');
			return $view;
		}
	}
	
	public function deactivateAction() {
		$request = $this->getRequest();
		if(!$request->isPost()) {
			// The user has manually gone to this page via URL. Redirect to inventory.
			return $this->redirect()->toRoute('game', array(
					'controller' => 'inventory'
			));
		} else {
			$bodySlot = $request->getPost()->bodySlot;
			$deactivationSuccessful = $this->getInventoryModel()->deactivateBodySlotForPlayer($bodySlot, $this->player->playerID);
				
			if(!$deactivationSuccessful) {
				// Suspicious behavior. The user has probably tampered with the html header.
				// To be done: Report event as suspicious behavior for this user.
				$message = "En feil oppstod under deaktivering av gjenstanden.";
			} else {
				// Return message to view
				$message = "Gjenstanden ble deaktivert.";
			}
				
			$view = new ViewModel(array_merge(
					$this->indexAction(),
					array('message' => $message)
			));
				
			// Re-render inventory
			$view->setTemplate('game/inventory/index');
			return $view;
		}
	}
}

?>