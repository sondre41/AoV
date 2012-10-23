<?php

namespace Game\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MapController extends AbstractActionController {
	protected $mapTable;
	protected $playerTable;
	protected $townTable;
	
    public function indexAction() {
    	// Get center map coordinates from the request
    	$centerLongitude = $this->params()->fromRoute('longitude', false);
    	$centerLatitude = $this->params()->fromRoute('latitude', false);
    	
    	// Get player information
    	$player = $this->getPlayerTable()->getPlayer(1);
    	
    	// Set map constants
    	$mapHeight = $mapWidth = 100;
    	$mapPartHeight = 11;
    	$mapPartWidth = 17;
    	
    	if(!$centerLongitude || !$centerLatitude) {
    		// No coordinates was registered. Center map at the players position.
    		$longitude = $this->getCenteredLongitude($player->longitude, $mapPartHeight, $mapHeight);
    		$latitude = $this->getCenteredLatitude($player->latitude, $mapPartWidth, $mapWidth);
    		$centerLongitude = $player->longitude;
    		$centerLatitude = $player->latitude;
    	} else {
    		// Both coordinates was registered, center both
    		$longitude = $this->getCenteredLongitude($centerLongitude, $mapPartHeight, $mapHeight);
    		$latitude = $this->getCenteredLatitude($centerLatitude, $mapPartWidth, $mapWidth);
    	}
    	
        return array(
        	'mapSquares' => $this->getMapTable()->getMapPart($mapPartHeight, $mapPartWidth, $longitude, $latitude),
        	'players' => $this->getPlayerTable()->getPlayersInPositionRange($mapPartHeight, $mapPartWidth, $longitude, $latitude)->toArray(),
        	'longitude' => $longitude,
        	'latitude' => $latitude,
        	'centerLongitude' => $centerLongitude,
        	'centerLatitude' => $centerLatitude,
        	'playerLongitude' => $player->longitude,
        	'playerLatitude' => $player->latitude,
        	'mapHeight' => $mapPartHeight,
        	'mapWidth' => $mapPartWidth,
        	'playerID' => 1
        );
    }
    
    public function buysquareAction() {
    	// Get coordinates from the request
    	$longitude = $this->params()->fromRoute('longitude', false);
    	$latitude = $this->params()->fromRoute('latitude', false);
    	
    	// Coordinates are required
    	if(!$longitude || !$latitude) {
    		return $this->redirect()->toRoute('game/map');
    	}
    	
    	// Check whether or not the player has the required amount of gold
    	$player = $this->getPlayerTable()->getPlayer(1);
    	if($player->gold < 1000) {
    		// Player doesn't have enough gold to buy this square. Redirect to map.
    		$message = "You do not have the necessary amount of gold (1000) to buy this map square.";
    	} else {
    		// Subtract the square cost from the players gold stack
    		$player->gold -= 1000;
    		$player->save();
    		
    		// Set the player as owner of the square
    		$this->getMapTable()->setMapSquareOwner($longitude, $latitude, 1);
    		
    		// Give confirmation message
    		$message = "You bought the map square ($longitude, $latitude) successfully.";
    	}
    	
    	$view = new ViewModel(array_merge(
    			$this->indexAction(),
    			array('message' => $message)
    	));
    		
    	// Re-render map
    	$view->setTemplate('game/map/index');
    	return $view;
    }
    
    public function establishtownAction() {
        // Get coordinates from the request
    	$longitude = $this->params()->fromRoute('longitude', false);
    	$latitude = $this->params()->fromRoute('latitude', false);
    	
    	// Coordinates are required
    	if(!$longitude || !$latitude) {
    		return $this->redirect()->toRoute('game/map');
    	}
    	
    	// Check if the player already has a town established
    	if($this->getTownTable()->playerOwnsTown(1)) {
    		return $this->redirect()->toRoute('game/map');
    	}
    	
    	// Check if the map square type with the given coordinates are of the correct type
    	$mapSquare = $this->getMapTable()->getMapSquare($longitude, $latitude);
    	if(!$mapSquare->type == 'plains') {
    		return $this->redirect()->toRoute('game/map');
    	}
    	
    	// Register a town at the given coordinates
    	$this->getMapTable()->setMapSquareType($longitude, $latitude, 'town');
    	$this->getTownTable()->createTown($longitude, $latitude, 1);
    	
    	$message = "Du opprettet en landsby i den valgte ruten ($longitude, $latitude).";
    	
    	$view = new ViewModel(array_merge(
    			$this->indexAction(),
    			array('message' => $message)
    	));
    	
    	// Re-render map
    	$view->setTemplate('game/map/index');
    	return $view;
    }
    
    public function moveplayerAction() {
    	// Get coordinates from request
    	$longitude = $this->params()->fromRoute('longitude', false);
    	$latitude = $this->params()->fromRoute('latitude', false);
    	
    	if($longitude && $latitude) {
	    	// Move player
	    	$this->getPlayerTable()->movePlayer($longitude, $latitude);
    	}
    	
    	// Redirect to map
    	return $this->redirect()->toRoute('game/map');
    }

    private function getCenteredLongitude($longitude, $mapPartHeight, $mapHeight) {
    	// Center map at the position, but not outside the map edges
    	$long = $longitude - (($mapPartHeight - 1) / 2);
    	
    	if($long <= 0) {
    		$long = 0;
    	} else if($long >= $mapHeight) {
    		$long = $mapHeight;
    	}
    	
    	return $long;
    }
    
    private function getCenteredLatitude($latitude, $mapPartWidth, $mapWidth) {
    	// Center map at the position, but not outside the map edges
    	$lati = $latitude - (($mapPartWidth - 1) / 2);
    	
    	if($lati <= 0) {
    		$lati = 0;
    	} else if($lati >= $mapWidth) {
    		$lati = $mapWidth;
    	}
    	
    	return $lati;
    }
    
    private function getMapTable() {
    	if (!$this->mapTable) {
    		$serviceManager = $this->getServiceLocator();
    		$this->mapTable = $serviceManager->get('Game\Models\MapTable');
    	}
    	return $this->mapTable;
    }
    
    private function getPlayerTable() {
    	if (!$this->playerTable) {
    		$serviceManager = $this->getServiceLocator();
    		$this->playerTable = $serviceManager->get('Game\Models\PlayerTable');
    	}
    	return $this->playerTable;
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