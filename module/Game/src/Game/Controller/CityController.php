<?php

namespace Game\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class CityController extends AbstractActionController {
	protected $cityTable;
	protected $playerTable;
	
	public function indexAction() {
    	$longitude = $this->params()->fromRoute('longitude', false);
    	$latitude = $this->params()->fromRoute('latitude', false);
    	
		if(!$longitude || !$latitude) {
	    	// Redirect to map controller
	    	return $this->redirect()->toRoute('game', array(
	    		'controller' => 'map'
	    	));
	    }
    	
		return array('playersInCity' => $this->getPlayerTable()->getPlayersAtPosition($longitude, $latitude));
	}
	
	private function getCityTable() {
		if (!$this->cityTable) {
			$serviceManager = $this->getServiceLocator();
			$this->cityTable = $serviceManager->get('Game\Model\CityTable');
		}
		return $this->cityTable;
	}
	
	private function getPlayerTable() {
		if (!$this->playerTable) {
			$serviceManager = $this->getServiceLocator();
			$this->playerTable = $serviceManager->get('Game\Model\PlayerTable');
		}
		return $this->playerTable;
	}
}

?>