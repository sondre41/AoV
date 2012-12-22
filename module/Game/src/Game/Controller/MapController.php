<?php

namespace Game\Controller;

use Zend\View\Model\ViewModel;

class MapController extends GameController {
    public function indexAction($moveToSquare = false) {
    	// Get center map coordinates from the request
    	$centerLongitude = $this->params()->fromRoute('longitude', false);
    	$centerLatitude = $this->params()->fromRoute('latitude', false);
    	
    	// Get player information
    	$player = $this->getPlayerTable()->getPlayer($this->playerId);
    	
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
    	
    	// Get info about the map squares to be displayed and get info about eventual players positioned on those squares
    	$mapSquares = $this->getMapTable()->getMapPart($mapPartHeight, $mapPartWidth, $longitude, $latitude);
    	$players = $this->getPlayerTable()->getPlayersInPositionRange($mapPartHeight, $mapPartWidth, $longitude, $latitude)->toArray();
    	
    	// Check for player traveling
    	if($player->areActionsBlocked() && $player->action == 'traveling') {
    		$playerIsTraveling = true;
    		
    		$minutes = floor($player->actionsBlockedTime() / 60);
    		$seconds = $player->actionsBlockedTime() % 60;
    		
    		$travelNotice = 'Du er er akkurat nå på reise til en ny rute.<br> Det gjenstår ';
	    	if($minutes > 0) {
	    		$travelNotice.= "$minutes minutt";
	    		if($minutes > 1) {
	    			$travelNotice.= "er";
	    		}
	    	}
	    	if($minutes > 0 && $seconds > 0) {
	    		$travelNotice.= " og ";
	    	}
	    	if($seconds > 0) {
	    		$travelNotice.= "$seconds sekund";
	    		if($seconds > 1) {
	    			$travelNotice.= "er";
	    		}
	    	}
	    	$travelNotice.= ' av reisen.';
    	} else {
    		$playerIsTraveling = false;
    		$travelNotice = '';
    	}
    	
        return array(
        	'mapSquares' => $mapSquares,
        	'players' => $players,
        	'centerLongitude' => $centerLongitude,
        	'centerLatitude' => $centerLatitude,
        	'playerLongitude' => $player->longitude,
        	'playerLatitude' => $player->latitude,
        	'mapHeight' => $mapPartHeight,
        	'mapWidth' => $mapPartWidth,
        	'playerID' => $this->player->playerID,
        	'moveToSquare' => $moveToSquare,
        	'playerIsTraveling' => $playerIsTraveling,
        	'travelNotice' => $travelNotice,
        	'longitude' => $longitude,
        	'latitude' => $latitude
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
    	$player = $this->getPlayerTable()->getPlayer($this->player->playerID);
    	if($player->gold < 1000) {
    		// Player doesn't have enough gold to buy this square. Redirect to map.
    		$message = "You do not have the necessary amount of gold (1000) to buy this map square.";
    	} else {
    		// Subtract the square cost from the players gold stack
    		$player->gold -= 1000;
    		$player->save();
    		
    		// Set the player as owner of the square
    		$this->getMapTable()->setMapSquareOwner($longitude, $latitude, $this->player->playerID);
    		
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
    	if($this->getTownTable()->playerOwnsTown($this->player->playerID)) {
    		return $this->redirect()->toRoute('game/map');
    	}
    	
    	// Check if the map square type with the given coordinates are of the correct type
    	$mapSquare = $this->getMapTable()->getMapSquare($longitude, $latitude);
    	if(!$mapSquare->type == 'plains') {
    		return $this->redirect()->toRoute('game/map');
    	}
    	
    	// Register a town at the given coordinates
    	$this->getMapTable()->setMapSquareType($longitude, $latitude, 'town');
    	$this->getTownTable()->createTown($longitude, $latitude, $this->player->playerID);
    	
    	$message = "Du opprettet en landsby i den valgte ruten ($longitude, $latitude).";
    	
    	$view = new ViewModel(array_merge(
    			$this->indexAction(),
    			array('message' => $message)
    	));
    	
    	// Re-render map
    	$view->setTemplate('game/map/index');
    	return $view;
    }
    
    public function moveAction() {
    	// Get coordinates from the request
    	$longitude = $this->params()->fromRoute('longitude', false);
    	$latitude = $this->params()->fromRoute('latitude', false);
    	 
    	// Coordinates are required
    	if(!$longitude || !$latitude) {
    		return $this->redirect()->toRoute('game/map');
    	}
    	
    	// Check that the player is not currently moving/actions are blocked
    	$player = $this->getPlayerTable()->getPlayer($this->playerId);
    	if($player->areActionsBlocked()) {
    		return $this->redirect()->toRoute('game/map');
    	}
    	
    	// Calculate travel time
    	$longitudeDiff = abs($player->longitude - $longitude);
    	$latitudeDiff = abs($player->latitude - $latitude);
    	
    	$squareDistance = sqrt(($longitudeDiff * $longitudeDiff) + ($latitudeDiff * $latitudeDiff));
    	$timeDistance = floor($squareDistance * 60);
    	$minutes = floor($timeDistance / 60);
    	$seconds = $timeDistance % 60;
    	
    	$message = "Er du sikker på at du ønsker å flytte deg til rute ($longitude, $latitude)?<br>";
    	$message.= "Reisen vil ta ";
    	if($minutes > 0) {
    		$message.= "$minutes minutt";
    		if($minutes > 1) {
    			$message.= "er";
    		}
    	}
    	if($minutes > 0 && $seconds > 0) {
    		$message.= " og ";
    	}
    	if($seconds > 0) {
    		$message.= "$seconds sekund";
    		if($seconds > 1) {
    			$message.= "er";
    		}
    	}
    	$message.= " å gjennomføre.<br>";
    	$message.= "Klikk på ruten på nytt for å starte forlytningen dit.";
    	
    	$view = new ViewModel(array_merge(
    			$this->indexAction(true),
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
    		$player = $this->getPlayerTable()->getPlayer($this->playerId);
	    	
	    	// Calculate travel time
	    	$longitudeDiff = abs($player->longitude - $longitude);
	    	$latitudeDiff = abs($player->latitude - $latitude);
	    	 
	    	$squareDistance = sqrt(($longitudeDiff * $longitudeDiff) + ($latitudeDiff * $latitudeDiff));
	    	$timeDistance = floor($squareDistance * 60);
	    	
	    	// Set player action and actions blocked
	    	$player = $this->getPlayerTable()->getPlayer($this->playerId);
	    	$player->extendActionsBlockedTime($timeDistance);
	    	$player->action = 'traveling';
	    	
	    	// Move player
	    	$player->longitude = $longitude;
	    	$player->latitude = $latitude;
	    	
	    	// Persist to DB
	    	$player->save();
    	}
    	
    	// Redirect to map
    	return $this->redirect()->toRoute('game/map');
    }
	
    public function squareAction() {
    	$longitude = $this->longitude = $this->params()->fromRoute('longitude', false);
    	$latitude = $this->latitude = $this->params()->fromRoute('latitude', false);
    	
    	if(! $longitude && ! $latitude) {
    		// Get the players coordinates
    		$player = $this->getPlayerTable()->getPlayer($this->playerId);
    		$longitude = $player->longitude;
    		$latitude = $player->latitude;
    	}
    	
    	$mapSquare = $this->getMapTable()->getMapSquare($longitude, $latitude);
    	$squareType = $mapSquare->type;
    	
    	// Redirect to the correct controller
    	switch($squareType) {
    		case 'forest':
    			$controller = 'forest';
    	}
    	
    	return $this->redirect()->toRoute('game', array(
    		'controller' => $controller,
    		'longitude' => $longitude,
    		'latitude' => $latitude
    	));
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
}

?>