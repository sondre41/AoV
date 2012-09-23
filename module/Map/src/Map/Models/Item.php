<?php

namespace Map\Models;

class Item {
	public $itemID;
	public $type;
	public $name;

	public function exchangeArray($data) {
		$this->itemID = (isset($data['itemID'])) ? $data['itemID'] : null;
		$this->type = (isset($data['type'])) ? $data['type'] : null;
		$this->name = (isset($data['name'])) ? $data['name'] : null;
	}
	
	public function toArray() {
		return get_object_vars($this);
	}
}

?>