<?php

namespace Map\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController {
	protected $mapTable;
	protected $playerTable;
	
    public function indexAction() {
    	$longitude = (int) $this->params()->fromRoute('longitude', 0);
    	$latitude = (int) $this->params()->fromRoute('latitude', 0);
    	
    	$player = $this->getPlayerTable()->getPlayer(1);
    	
    	$mapHeight = $mapWidth = 100;
    	$mapPartHeight = 11;
    	$mapPartWidth = 17;
    	
    	if(!$longitude && !$latitude) {
    		$longitude = $player->longitude - (($mapPartHeight - 1) / 2);
    		if($longitude <= 0) {
    			$longitude = 0;
    		} else if($longitude >= $mapHeight) {
    			$longitude = $mapHeight;
    		}
    		$latitude = $player->latitude - (($mapPartWidth - 1) / 2);
    		if($latitude <= 0) {
    			$latitude = 0;
    		} else if($latitude >= $mapWidth) {
    			$latitude = $mapWidth;
    		}
    	} else if(!$longitude) {
    		$longitude = 0;
    	} else if(!$latitude) {
    		$latitude = 0;
    	}
    	
    	$player = $this->getPlayerTable()->getPlayer(1);
    	
        return new ViewModel(array(
        	'mapSquares' => $this->getMapTable()->getMapPart($mapPartHeight, $mapPartWidth, $longitude, $latitude),
        	'players' => $this->getPlayerTable()->getPlayersInPositionRange($mapPartHeight, $mapPartWidth, $longitude, $latitude)->toArray(),
        	'longitude' => $longitude,
        	'latitude' => $latitude,
        	'mapHeight' => $mapPartHeight,
        	'mapWidth' => $mapPartWidth
        ));
    }
    
    public function moveplayerAction() {
    	$longitude = (int) $this->params()->fromRoute('longitude', 0);
    	$latitude = (int) $this->params()->fromRoute('latitude', 0);
    	
    	// Move player
    	$this->getPlayerTable()->movePlayer($longitude, $latitude);
    	
    	return $this->redirect()->toRoute('map');
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
}

?>