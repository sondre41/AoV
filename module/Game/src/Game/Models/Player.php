<?php

namespace Game\Models;

use Zend\Db\RowGateway\RowGateway;

class Player extends RowGateway {
	protected $abilityLvlCaps = array();
	
	public function actionsBlockedTime() {
		$time = strtotime($this->actionsBlockedUntil);
		if($time > time()) {
			return $time - time();
		} else {
			return 0;
		}
	}
	
	public function isActionsBlocked() {
		if($this->actionsBlockedTime() > 0) {
			return true;
		}
		return false;
	}
	
	public function extendActionsBlockedTime($seconds) {
		$timestamp = time() + $seconds;
		$this->actionsBlockedUntil = date('YmdHis', $timestamp);
	}
	
	public function getAbilityLvlInfo() {
		if(count($this->abilityLvlCaps) == 0) {
			$this->setAbilityLvlCaps();
		}
		
		$abilityLvlInfo = array(
			'atck' => array(),
			'defs' => array(),
			'prec' => array(),
			'agil' => array()
		);
		
		foreach($abilityLvlInfo as $ability => $info) {
			$abilityLvl = 0;
			foreach($this->abilityLvlCaps as $lvl => $cap) {
				if($this->$ability >= $cap) {
					$abilityLvl = $lvl;
				} else {
					break;
				}
			}
			
			$abilityLvlInfo[$ability]['lvl'] = $abilityLvl;
			$currentLvlCap = $this->abilityLvlCaps[$abilityLvl];
			$nextLvlCap = $this->abilityLvlCaps[$abilityLvl + 1];
			$abilityLvlInfo[$ability]['progressToNextLvl'] = $this->$ability - $currentLvlCap;
			$abilityLvlInfo[$ability]['nextLvlCap'] = $nextLvlCap - $currentLvlCap;
		}
		
		return $abilityLvlInfo;
	}
	
	private function setAbilityLvlCaps() {
		// Set up ability level caps
		$this->abilityLvlCaps[0] = 0;
		$this->abilityLvlCaps[1] = 5;
		
		for($i = 2; $i < 100; $i++) {
			if($i < 5) {
				$factor = 1.6;
			} else if($i < 10) {
				$factor = 1.4;
			} else if($i < 20) {
				$factor = 1.15;
			} else if($i < 30) {
				$factor = 1.1;
			} else if($i < 50) {
				$factor = 1.075;
			} else if($i < 75) {
				$factor = 1.03;
			} else {
				$factor = 1.01;
			}
			
			$this->abilityLvlCaps[$i] = floor($this->abilityLvlCaps[$i - 1] * $factor);
		}
	}
}

?>