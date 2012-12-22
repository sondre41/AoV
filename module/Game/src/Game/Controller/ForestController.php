<?php

namespace Game\Controller;

use Zend\View\Model\ViewModel;

class ForestController extends MapSquareController {
	protected $type = 'forest';
	
	public function indexAction() {
		$view = new ViewModel();
		$view->setTemplate('game/forest/index');
		$view->setVariable('blocked', true);
		
		if($this->player->areActionsBlocked()) {
			$message = "Du gjør en annen aksjon. Du må vente i " . $this->player->actionsBlockedTime()
					 . " sekunder før du kan påbegynne huggingen av et tre.";
			$view->setVariable('message', $message);
				
			return $view;
		}
		
		$playerHasAxeActive = $this->getInventoryModel()->playerHasItemTypeActive($this->playerId, 'axe');
		if(! $playerHasAxeActive) {
			$message = "Du må ha en øks aktiv i din inventar for å kunne hugge et tre.";
			$view->setVariable('message', $message);
			
			return $view;
		}
		
		$request = $this->getRequest();
		if($request->isPost()) {
			// Give the player a lumber item
			$lumberItem = $this->getItemTable()->getItem('log');
			$this->getInventoryTable()->giveItemToPlayer($lumberItem->itemID, $this->playerId);
				
			// Block actions for the player for 3 minutes
			$secondsToBlock = 15;
			$this->player->extendActionsBlockedTime($secondsToBlock);
			$this->player->save();
				
			// Give message to player
			$message = "Du har nå påbegynt hugging av et tre. Det tar 3 minutter før du er ferdig.";
			$view->setVariable('message', $message);
		} else {
			$view->setVariable('blocked', false);
		}
				
		return $view;
	}
}

?>