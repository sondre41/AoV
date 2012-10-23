<?php

namespace Game\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class TownController extends AbstractActionController {
	protected $inventoryModel;
	protected $inventoryTable;
	protected $playerTable;
	protected $townBuildingTable;
	protected $townTable;
	
	public function indexAction() {
		// Get coordinates from the request
		$longitude = $this->params()->fromRoute('longitude', false);
		$latitude = $this->params()->fromRoute('latitude', false);
		 
		// Coordinates are required
		if(!$longitude || !$latitude) {
			return $this->redirect()->toRoute('game/map');
		}
		
		// Get town statistics
		$town = $this->getTownTable()->getTown($longitude, $latitude);
		if(!$town) {
			return $this->redirect()->toRoute('game/map');
		}
		
		// Get player inventory statistics
		$inventory = $this->getInventoryModel()->getPlayerInventory(1);

		// Find out if this town has a Resource Storage built
		$townHasStorage = $this->getTownBuildingTable()->townHasBuilding('resource storage', $town->townID);
		
		return array(
			'longitude' => $longitude,
			'latitude' => $latitude,
			'town' => $town,
			'townHasStorage' => $townHasStorage,
			'playerInventory' => $inventory->toArray(),
			'completedBuildings' => $this->getTownBuildingTable()->getCompletedTownBuildingsForTown($town->townID),
			'uncompletedBuilding' => $this->getTownBuildingTable()->getUncompletedTownBuildingForTown($town->townID)
		);
	}
	
	public function buildAction() {
		// Get coordinates from the request
		$longitude = $this->params()->fromRoute('longitude', false);
		$latitude = $this->params()->fromRoute('latitude', false);
			
		// Coordinates are required
		if(!$longitude || !$latitude) {
			return $this->redirect()->toRoute('game/map');
		}
		
		// Get the submited build-with-resource-type value and the resource container value
		$request = $this->getRequest();
		$resourceType = strtolower($request->getPost()->buildSubmit);
		$resourceContainer = $request->getPost()->resourceContainer;
		
		if($resourceType != 'lumber' && $resourceType != 'stone' && $resourceType != 'log' &&
		   $resourceContainer != 'storage' && $resourceContainer != 'inventory') {
			return $this->redirect()->toRoute('game/town');
		}
		
		$town = $this->getTownTable()->getTown($longitude, $latitude);
		
		if($resourceContainer == 'storage') {
			// Check if the town has has a quantity of one or more of the submited resource type in its resource storage
			if($town->$resourceType <= 0) {
				return $this->redirect()->toRoute('game/town');
			}
			
			// Reduce the amount of available resources for building in the town
			$this->getTownTable()->reduceAvailableResources($resourceType, $town->townID);
		} else {
			// Check if the player has a quantity of one or more of the submited resource type in his/her inventory
			$inventory = $this->getInventoryModel()->getPlayerInventory(1)->toArray();
			
			$resourceValue = 0;
			$lastResourceMatchInventoryID = false;
			foreach($inventory as $inventoryItem) {
				if($inventoryItem['name'] == $resourceType) {
					$resourceValue++;
					$lastResourceMatchInventoryID = $inventoryItem['inventoryID'];
				}
			}
			
			if($resourceValue <= 0) {
				return $this->redirect()->toRoute('game/town');
			}
			
			// Delete a resource of the submited resource type from the players inventory
			$this->getInventoryTable()->deleteItemForPlayer($lastResourceMatchInventoryID, 1);
		}
		
		// Reduce resources left of building for the current building project of the town
		$this->getTownBuildingTable()->reduceResourceLeftForBuilding($resourceType, $town->townID);
		
		// Update players waiting time with 3 minutes
		$player = $this->getPlayerTable()->getPlayer(1);
		//$player->extendActionsBlockedTime(180);
		
		// Give message to the user
		$indexActionViewArray = $this->indexAction();
		$message = "Du påbegynte påbygging av " . $indexActionViewArray['uncompletedBuilding']->type . ".";
		
		$view = new ViewModel(array_merge(
				$indexActionViewArray,
				array(
					'message' => $message
		)));
		
		$view->setTemplate('game/town/index');
		
		return $view;
	}
	
	public function buildingtreeAction() {
		// Get coordinates from the request
		$longitude = $this->params()->fromRoute('longitude', false);
		$latitude = $this->params()->fromRoute('latitude', false);
			
		// Coordinates are required
		if(!$longitude || !$latitude) {
			return $this->redirect()->toRoute('game/map');
		}
		
		$town = $this->getTownTable()->getTown($longitude, $latitude);
		
		return array(
			'longitude' => $longitude,
			'latitude' => $latitude,
			'buildings' => $this->getTownBuildingTable()->getAllBuildings(),
			'completedBuildings' => $this->getTownBuildingTable()->getCompletedTownBuildingsForTown($town->townID)->toArray(),
			'uncompletedBuilding' => $this->getTownBuildingTable()->getUncompletedTownBuildingForTown($town->townID)
		);
	}
	
	public function startbuildAction() {
		// Get coordinates from the request
		$longitude = $this->params()->fromRoute('longitude', false);
		$latitude = $this->params()->fromRoute('latitude', false);
			
		// Coordinates are required
		if(!$longitude || !$latitude) {
			return $this->redirect()->toRoute('game/map');
		}
		
		$request = $this->getRequest();
		
		if(!$request->isPost()) {
			return $this->redirect()->toRoute('game/town');
		}
		
		// Get building type from request
		$buildingTypeToBuild = $this->getRequest()->getPost()->buildingToStart;
		
		// Check that the town is not currently on a building project
		$town = $this->getTownTable()->getTown($longitude, $latitude);
		$buildingInProgress = $this->getTownBuildingTable()->getUncompletedTownBuildingForTown($town->townID);
		if($buildingInProgress) {
			return $this->redirect()->toRoute('game/town');
		}
		
		// Check that the chosen building type is not already built
		$completedBuildings = $this->getTownBuildingTable()->getCompletedTownBuildingsForTown($town->townID);
		$chosenBuildingIsBuilt = false;
		foreach($completedBuildings as $completedBuilding) {
			if($buildingTypeToBuild == $completedBuilding->type) {
				$chosenBuildingIsBuilt = true;
				break;
			}
		}
		
		if($chosenBuildingIsBuilt) {
			return $this->redirect()->toRoute('game/town');
		}
		
		// Start a building project of the chosen building
		$this->getTownBuildingTable()->createTownBuilding($town->townID, $buildingTypeToBuild);
		
		// Redirect to town index
		$indexActionViewArray = $this->indexAction();
		$message = "Du startet byggingen av " . $buildingTypeToBuild . ".";
		
		$view = new ViewModel(array_merge(
				$indexActionViewArray,
				array(
					'message' => $message
		)));
		
		$view->setTemplate('game/town/index');
		
		return $view;
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
	
	private function getPlayerTable() {
		if (!$this->playerTable) {
			$serviceManager = $this->getServiceLocator();
			$this->playerTable = $serviceManager->get('Game\Models\PlayerTable');
		}
		return $this->playerTable;
	}
	
	private function getTownBuildingTable() {
		if (!$this->townBuildingTable) {
			$serviceManager = $this->getServiceLocator();
			$this->townBuildingTable = $serviceManager->get('Game\Models\TownBuildingTable');
		}
		return $this->townBuildingTable;
	}
	
	private function getTownTable() {
		if (!$this->townTable) {
			$serviceManager = $this->getServiceLocator();
			$this->townTable = $serviceManager->get('Game\Models\TownTable');
		}
		return $this->townTable;
	}
}

?>