<?php

namespace Town\Controller;

use Zend\EventManager\EventManagerInterface;

use Game\Controller\MapSquareController;

class BuildingController extends MapSquareController {
	protected $type = 'town';
	protected $alwaysViewable = true;
	
	protected $buildingType;
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
    	parent::init();
    	
    	// Player must be in the town to be able to visit the buildings
    	if(! $this->isPlayerOnSquare) {
    		return $this->redirect()->toRoute('game');
    	}
		
		// Check that the town actually has the requested building built
		$town = $this->reloadTown();
		$hasItemStorage = $this->getTownBuildingTable()->townHasBuilding($this->buildingType, $town->townID);
		if(!$hasItemStorage) {
			return $this->redirect()->toRoute('town/' . $this->longitude . '/' . $this->latitude);
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