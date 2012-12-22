<?php

namespace Game\Controller;

use Zend\EventManager\EventManagerInterface;
use Zend\View\Model\ViewModel;

class MapSquareController extends GameController {
	protected $type;
	protected $longitude;
	protected $latitude;
	protected $alwaysViewable = false;
	protected $isPlayerOnSquare = false;
	
	public function setEventManager(EventManagerInterface $eventManager) {
		parent::setEventManager($eventManager);
			
		// Attach init method to be run before controller action
		$eventManager->attach('dispatch', array($this, 'init'), 100);
		
		// Attach view model (action result) modification method to be run after controller action
		$eventManager->attach('dispatch', array($this, 'setViewVariables'), -100);
	}
	
	public function init() {
		parent::init();
		
		// Check whether or not coordinates are set
		$longitude = $this->longitude = $this->params()->fromRoute('longitude', false);
		$latitude = $this->latitude = $this->params()->fromRoute('latitude', false);
		
		if(! $longitude && ! $latitude) {
			// Coordinates not set. Get coordinates from player.
			return $this->redirect()->toRoute('game');
		}
		
		$mapSquare = $this->getMapTable()->getMapSquare($longitude, $latitude);
		
		// Check if the requested map square is actually handled by the requested controller (a child of this)
		if($mapSquare->type != $this->type) {
			return $this->redirect()->toRoute('game');
		}
		
		$player = $this->player = $this->getPlayerTable()->getPlayer($this->playerId);
		
		// Check if the player is placed on the requested map square
		if($player->longitude == $mapSquare->longitude && $player->latitude = $mapSquare->latitude) {
			$this->isPlayerOnSquare = true;
		}
	}
	
	public function setViewVariables() {
		// Get the view model
		$viewModel = $this->getEvent()->getResult();
		 
		// Set variable for whether or not the player is currently at this specific map square
		$viewModel->setVariable('isPlayerOnSquare', $this->isPlayerOnSquare);
	}
}

?>