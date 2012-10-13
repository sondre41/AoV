<?php

namespace Game\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class TraininggroundsController extends AbstractActionController {
	protected $playerTable;
	
	public function indexAction() {
		$player = $this->getPlayerTable()->getPlayer(1);
		
		return array(
			'player' => $player,
			'abilityLvlInfo' => $player->getAbilityLvlInfo(),
			'blocked' => $player->isActionsBlocked()
		);
	}
	
	public function trainAction() {
		// Check if request is of type POST
		$request = $this->getRequest();
		if(!$request->isPost()) {
			// Redirect to index
			return $this->redirect()->toRoute('game/traininggrounds');
		}
		
		// Check if players actions are blocked
		$player = $this->getPlayerTable()->getPlayer(1);
		if($player->isActionsBlocked()) {
			// Redirect to index
			$view = new ViewModel(array(
				'player' => $player,
				'blocked' => true
			));
			$view->setTemplate('game/traininggrounds/index');
			return $view;
		}
		
		// Get ability from POST request
		$ability = $request->getPost()->ability;
		
		$abilities = array(
				'atck',
				'defs',
				'prec',
				'agil'
		);
		
		if(!$ability || !in_array($ability, $abilities)) {
			// Ability to train not set or not of a correct value. Redirect to index.
			return $this->redirect()->toRoute('game', array(
				'controller' => 'traininggrounds'
			));
		}
		
		// Increase the players ability value and set the players actions blocked for 60 seconds
		$player->$ability++;
		$player->extendActionsBlockedTime(60);
		
		// Persist to DB
		$player->save();
		
		$view = new ViewModel(array(
			'player' => $player,
			'abilityLvlInfo' => $player->getAbilityLvlInfo(),
			'blocked' => true
		));
		$view->setTemplate('game/traininggrounds/index');
		return $view;
	}
	
	private function getPlayerTable() {
		if (!$this->playerTable) {
			$serviceManager = $this->getServiceLocator();
			$this->playerTable = $serviceManager->get('Game\Models\PlayerTable');
		}
		return $this->playerTable;
	}
}

?>