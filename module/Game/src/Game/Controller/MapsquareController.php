<?php

namespace Game\Controller;

use Zend\Mvc\MvcEvent;

use Zend\EventManager\EventManagerAwareInterface;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class MapsquareController extends AbstractActionController {
	protected $playerTable;
	
	public function __construct() {
		$this->getEventManager()->attach('*', array($this, 'preDispatch'), 1000);
		echo "construct";
	}
	
	public function preDispatch() {
		echo "This is preDispatch()!";
	}
	
// 	protected function attachDefaultListeners() {
// 		parent::attachDefaultListeners();
		
// 		// Get longitude and latitude from request
// 		$longitude = (int) $this->params()->fromRoute('longitude', 0);
// 		$latitude = (int) $this->params()->fromRoute('latitude', 0);
		 
// 		if(!$longitude || !$latitude) {
// 			// Redirect to map controller
// 			return $this->redirect()->toRoute('game', array(
// 					'controller' => 'map'
// 			));
// 		}
// 	}
	
	// Class initialization
// 	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
// 	{
// 		parent::setServiceLocator($serviceLocator);
		
// 		// Get longitude and latitude from request
// 		$longitude = (int) $this->params()->fromRoute('longitude', 0);
//     	$latitude = (int) $this->params()->fromRoute('latitude', 0);
    	
//     	if(!$longitude || !$latitude) {
//     		// Redirect to map controller
//     		return $this->redirect()->toRoute('game', array(
//     				'controller' => 'map'
//     		));
//     	}
// 	}

	public function indexAction() {
		echo "index";
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