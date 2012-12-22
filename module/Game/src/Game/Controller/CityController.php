<?php

namespace Game\Controller;

class CityController extends GameController {
	public function indexAction() {
    	$longitude = $this->params()->fromRoute('longitude', false);
    	$latitude = $this->params()->fromRoute('latitude', false);
    	
		if(!$longitude || !$latitude) {
	    	// Redirect to map controller
	    	return $this->redirect()->toRoute('game', array(
	    		'controller' => 'map'
	    	));
	    }
	    
	    $playersInCity = $this->getPlayerTable()->getPlayersAtPosition($longitude, $latitude);
    	
		return array(
			'playersInCity' => $playersInCity
		);
	}
}

?>