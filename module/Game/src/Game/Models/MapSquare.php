<?php

namespace Game\Models;

class MapSquare {
	public $longitude;
	public $latitude;
	public $type;

	public function exchangeArray($data) {
		$this->longitude = (isset($data['longitude'])) ? $data['longitude'] : null;
		$this->latitude = (isset($data['latitude'])) ? $data['latitude'] : null;
		$this->type = (isset($data['type'])) ? $data['type'] : null;
	}
	
	public function toArray() {
		return get_object_vars($this);
	}
}

?>