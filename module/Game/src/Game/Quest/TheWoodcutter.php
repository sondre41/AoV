<?php

namespace Game\Quest;

class TheWoodcutter extends Quest implements QuestInterface {
	public function checkPrerequisites() {
		$this->completed = true;
	}
}

?>