<?php

namespace Map\Models;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql;

class PlayerTable extends AbstractTableGateway {
	protected $table ='player';
	
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
		$this->resultSetPrototype = new ResultSet();
		$this->resultSetPrototype->setArrayObjectPrototype(new Player());
		$this->initialize();
	}
	
	public function getPlayersInPositionRange($height = 11, $width = 13, $longitude = 0, $latitude = 0) {
		$where = new Sql\Where();
		$where->greaterThanOrEqualTo('longitude', $longitude)
			  ->greaterThanOrEqualTo('latitude', $latitude)
			  ->lessThan('longitude', $longitude + $height)
			  ->lessThan('latitude', $latitude + $width);
		
		return $this->select($where);
	}
	
	public function getPlayer($playerID) {
		$rowset = $this->select(array('playerID' => $playerID));
		$row = $rowset->current();
		if (!$row) {
			throw new \Exception("No player with $playerID could be found.");
		}
		return $row;
	}
	
	public function movePlayer($newLongitude, $newLatitude) {
		$this->update(array('longitude' => $newLongitude,
							'latitude' => $newLatitude));
	}
	
	public function setPlayerActionsBlocked($secondsToBlock) {
		$timestamp = time() + $secondsToBlock;
		$this->update(array('actionsBlockedUntil' => date('YmdHis', $timestamp)));
	}
}

?>