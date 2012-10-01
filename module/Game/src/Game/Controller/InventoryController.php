<?php

namespace Game\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class InventoryController extends AbstractActionController {
	protected $playerModel;
	protected $playerTable;
	protected $inventoryModel;
	protected $inventoryTable;
	
	public function indexAction() {
		return array('inventoryItems' => $this->getInventoryModel()->getPlayerInventory(1)->toArray());
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
			$nrOfItemsDeleted = $this->getInventoryTable()->deleteItemForPlayer($inventoryID, 1);
			
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
			$activationSuccessful = $this->getInventoryModel()->activateItemForPlayer($inventoryID, 1, $hand);
			
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
			$deactivationSuccessful = $this->getInventoryModel()->deactivateBodySlotForPlayer($bodySlot, 1);
				
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
	
	private function getPlayerModel() {
		if (!$this->playerModel) {
			$serviceManager = $this->getServiceLocator();
			$this->playerModel = $serviceManager->get('Game\Models\PlayerModel');
		}
		return $this->playerModel;
	}
	
	private function getPlayerTable() {
		if (!$this->playerTable) {
			$serviceManager = $this->getServiceLocator();
			$this->playerTable = $serviceManager->get('Game\Models\PlayerTable');
		}
		return $this->playerTable;
	}
	
	private function getInventoryModel() {
		if (!$this->inventoryModel) {
			$serviceManager = $this->getServiceLocator();
			$this->inventoryModel = $serviceManager->get('Game\Models\InventoryModel');
		}
		return $this->inventoryModel;
	}
	
	private function getInventoryTable() {
		if (!$this->inventoryTable) {
			$serviceManager = $this->getServiceLocator();
			$this->inventoryTable = $serviceManager->get('Game\Models\InventoryTable');
		}
		return $this->inventoryTable;
	}
}

?>