<?php

namespace Game\Quest;

use Zend\ServiceManager\ServiceManager;

class Quest {
	protected $completed = false;
	
	protected $serviceManager;
	
	public function __construct(ServiceManager $serviceManager) {
		$this->serviceManager = $serviceManager;
	}
	
	public function isCompleted() {
		return $this->completed;
	}
}

?>