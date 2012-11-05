<?php

namespace Town\Controller;

use Game\Controller\GameController;
use Zend\EventManager\EventManagerInterface;

class BuildingController extends GameController {
	protected $buildingType;
	protected $latitude;
	protected $longitude;
	protected $town;
	
	public function setEventManager(EventManagerInterface $eventManager)
	{
		parent::setEventManager($eventManager);
		 
		// Attach init method to be run before controller action
		$eventManager->attach('dispatch', array($this, 'init'), 100);
		 
		// Attach view model (action result) modification method to be run after controller action
		$eventManager->attach('dispatch', array($this, 'setCoordinateViewVariables'), -100);
	}
	
    public function init() {
    	// Get coordinates from the request
		$longitude = $this->longitude = $this->params()->fromRoute('longitude', false);
		$latitude = $this->latitude = $this->params()->fromRoute('latitude', false);
			
		// Coordinates are required
		if(!$longitude || !$latitude) {
			return $this->redirect()->toRoute('town');
		}
		
		// Check that the town actually has the requested building built
		$town = $this->reloadTown();
		$hasItemStorage = $this->getTownBuildingTable()->townHasBuilding($this->buildingType, $town->townID);
		if(!$hasItemStorage) {
			return $this->redirect()->toRoute('town/' . $longitude . '/' . $latitude);
		}
    }
    
    public function setCoordinateViewVariables() {
    	// Get the view model
    	$viewModel = $this->getEvent()->getResult();
    	
    	// Set variables for longitude and latitude coordinates
    	$viewModel->setVariable('longitude', $this->longitude);
    	$viewModel->setVariable('latitude', $this->latitude);
    }
    
    protected function reloadTown() {
    	return $this->town = $this->getTownTable()->getTown($this->longitude, $this->latitude);
    }
}

?>