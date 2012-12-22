<?php

namespace Game\Controller;

class QuestController extends GameController {
	public function indexAction() {
		$playerQuests = $this->getQuestModel()->getAllPlayerQuests($this->player->playerID);
    	
		//$this->getQuestModel()->checkForPlayerMissionsCompleted($this->player->playerID);
		
		return array(
			'quests' => $playerQuests
		);
	}
}

?>