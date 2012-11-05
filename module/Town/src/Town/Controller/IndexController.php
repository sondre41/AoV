<?php

namespace Town\Controller;

use Game\Controller\GameController;
use Zend\View\Model\ViewModel;

class IndexController extends GameController {
	public function indexAction() {
		// Get coordinates from the request
		$longitude = $this->params()->fromRoute('longitude', false);
		$latitude = $this->params()->fromRoute('latitude', false);
		 
		// Coordinates are required
		if(!$longitude || !$latitude) {
			// If coordinates are not given, try to find the default town for this user
			$town = $this->getTownTable()->getPlayersDefaultTown(1);
			
			// Set coordinates
			if($town) {
				$longitude = $town->longitude;
				$latitude = $town->latitude;
			}
		}
		
		// Get town statistics if not already gotten above
		if(!isset($town)) {
			$town = $this->getTownTable()->getTown($longitude, $latitude);
		}
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
			return $this->redirect()->toRoute('town');
		}
		
		$town = $this->getTownTable()->getTown($longitude, $latitude);
		
		if($resourceContainer == 'storage') {
			// Check if the town has has a quantity of one or more of the submited resource type in its resource storage
			if($town->$resourceType <= 0) {
				return $this->redirect()->toRoute('town');
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
				return $this->redirect()->toRoute('town');
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
		
		$view->setTemplate('town/index/index');
		
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
		
		$this->getTownBuildingTable()->getCompletedTownBuildingsForTown($town->townID);
		
		$completedBuildings = $this->getTownBuildingTable()->getCompletedTownBuildingsForTown($town->townID);
		
		return array(
			'longitude' => $longitude,
			'latitude' => $latitude,
			'buildings' => $this->getTownBuildingTable()->getAllBuildings(),
			'completedBuildings' => $completedBuildings,
			'uncompletedBuilding' => $this->getTownBuildingTable()->getUncompletedTownBuildingForTown($town->townID),
			'buildingsWithPrerequisitesObtained' => $this->getTownBuildingTable()->getBuildingsWithPrerequisitesObtained($completedBuildings)
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
			return $this->redirect()->toRoute('town');
		}
		
		// Get building type from request
		$buildingTypeToBuild = $this->getRequest()->getPost()->buildingToStart;
		
		// Check that the town is not currently on a building project
		$town = $this->getTownTable()->getTown($longitude, $latitude);
		$buildingInProgress = $this->getTownBuildingTable()->getUncompletedTownBuildingForTown($town->townID);
		
		// Check that the chosen building type is not already built
		$completedBuildings = $this->getTownBuildingTable()->getCompletedTownBuildingsForTown($town->townID);
		$chosenBuildingIsBuilt = false;
		foreach($completedBuildings as $completedBuilding) {
			if($buildingTypeToBuild == $completedBuilding) {
				$chosenBuildingIsBuilt = true;
				break;
			}
		}
		
		// Check that the building has all its prerequisites obtained
		$buildingsWithPrerequisitesObtained = $this->getTownBuildingTable()->getBuildingsWithPrerequisitesObtained($completedBuildings);
		$prerequisitesObtained = true;
		if(in_array($buildingTypeToBuild, $buildingsWithPrerequisitesObtained)) {
			$prerequisitesNotObtained = false;
		}
		
		// Redirect if any of the above checks failed
		if($buildingInProgress || $chosenBuildingIsBuilt || $prerequisitesNotObtained) {
			return $this->redirect()->toRoute('town');
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
		
		$view->setTemplate('town/index/index');
		
		return $view;
	}
	
	public function stopbuildAction() {
		// Get coordinates from the request
		$longitude = $this->params()->fromRoute('longitude', false);
		$latitude = $this->params()->fromRoute('latitude', false);
			
		// Coordinates are required
		if(!$longitude || !$latitude) {
			return $this->redirect()->toRoute('game/map');
		}
		
		$request = $this->getRequest();
		
		if($request->isPost()) {
			// Get answer on "Do you want to stop?" question
			$answer = $request->getPost('stopBuild', 'No');
			
			if(strtolower($answer) == 'yes') {
				// Stop the building project
				$town = $this->getTownTable()->getTown($longitude, $latitude);
				$this->getTownBuildingTable()->stopBuildingProject($town->townID);
				
				// Redirect to town index
				$indexActionViewArray = $this->indexAction();
				$message = "Du stoppet bygningsprosjektet.";
				
				$view = new ViewModel(array_merge(
						$indexActionViewArray,
						array(
							'message' => $message
				)));
				
				$view->setTemplate('town/index/index');
				
				return $view;
			} else {
				return $this->redirect()->toRoute('town/index/index');
			}
		}
		
		return array(
			'longitude' => $longitude,
			'latitude' => $latitude
		);
	}
}

?>