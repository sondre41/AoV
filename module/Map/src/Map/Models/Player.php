<?php

namespace Map\Models;

class Player {
	public $playerID;
	public $longitude;
	public $latitude;
	public $actionsBlockedUntil;

	public function exchangeArray($data) {
		$this->playerID = (isset($data['playerID'])) ? $data['playerID'] : null;
		$this->longitude = (isset($data['longitude'])) ? $data['longitude'] : null;
		$this->latitude = (isset($data['latitude'])) ? $data['latitude'] : null;
		$this->actionsBlockedUntil = (isset($data['actionsBlockedUntil'])) ? strtotime($data['actionsBlockedUntil']) : null;

	}
	
	public function toArray() {
		return get_object_vars($this);
	}
	
	public function actionsBlockedTime() {
		if($this->actionsBlockedUntil > time()) {
			return $this->actionsBlockedUntil - time();
		} else {
			return 0;
		}
	}
}

?>