<?php

namespace Game\Models;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;

class PlayerTable extends AbstractTableGateway {
	protected $table = 'player';
	protected $primary = 'playerID';
	
	public function __construct(Adapter $adapter) {
		// Set DB adapter
		$this->adapter = $adapter;
		
		// Set table gateway feture set
		$featureSet = new Feature\FeatureSet();
		$featureSet->addFeature(new Feature\RowGatewayFeature(new Player($this->primary, $this->table, $this->adapter, $this->sql)));
		$this->featureSet = $featureSet;
		
		// Initialize
		$this->initialize();
	}
	
	// Get a specific player from DB
	public function getPlayer($playerID) {
		$rowset = $this->select(array('playerID' => $playerID));
		$row = $rowset->current();
	
		if (!$row) {
			throw new \Exception("No player with $playerID could be found.");
		}
	
		return $row;
	}
	
	// Get all players within a specific position range
	public function getPlayersInPositionRange($height = 11, $width = 13, $longitude = 0, $latitude = 0) {
		$where = new Where();
		$where->greaterThanOrEqualTo('longitude', $longitude)
			  ->greaterThanOrEqualTo('latitude', $latitude)
			  ->lessThan('longitude', $longitude + $height)
			  ->lessThan('latitude', $latitude + $width);
		
		return $this->select($where);
	}
	
	// Get all players positioned at the specific position
	public function getPlayersAtPosition($longitude, $latitude) {
		$where = new Where();
		$where->equalTo('longitude', $longitude)
			  ->equalTo('latitude', $latitude);
		
		return $this->select($where);
	}
	
	// Increment a specific ability for a specific player with an amount
	public function incrementPlayerAbility($playerID, $ability, $increment = 1) {
		// Set the correct ability to be incremented
		$set = array();
		
		$set[$ability] = new Expression("($ability + $increment)");
	
// 		if($ability == 'attack') {
// 			$set['attack'] = new Expression("(attack + $increment)");
// 		} else if($ability == 'defense') {
// 			$set['defense'] = new Expression("(defense + $increment)");
// 		} else if($ability == 'precision') {
// 			$set['precisionn'] = new Expression("(precisionn + $increment)");
// 		} else if($ability == 'agility') {
// 			$set['agility'] = new Expression("(agility + $increment)");
// 		}
	
		// Update
		$this->update($set, array('playerID' => $playerID));
	}
	
	// Move a specific player to a new position
	public function movePlayer($newLongitude, $newLatitude) {
		$this->update(array('longitude' => $newLongitude,
							'latitude' => $newLatitude));
	}
	
	// Set the actionsBlocked time for a specific player
	public function setPlayerActionsBlocked($playerID, $secondsToBlock) {
		$timestamp = time() + $secondsToBlock;
		$this->update(array('actionsBlockedUntil' => date('YmdHis', $timestamp)),
					  array('playerID' => $playerID));
	}
}

?>