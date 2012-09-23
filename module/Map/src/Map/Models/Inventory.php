<?php

namespace Map\Models;

class Inventory {
	public $inventoryID;
	public $playerID;
	public $itemID;

	public function exchangeArray($data) {
		$this->inventoryID = (isset($data['inventoryID'])) ? $data['inventoryID'] : null;
		$this->playerID = (isset($data['playerID'])) ? $data['playerID'] : null;
		$this->itemID = (isset($data['itemID'])) ? $data['itemID'] : null;
	}
	
	public function toArray() {
		return get_object_vars($this);
	}
}

?>