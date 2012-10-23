<?php

namespace Game\Models;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Expression;
use Zend\ServiceManager\ServiceManager;

class TownTable extends AbstractTableGateway {
	protected $table = 'town';
	protected $serviceManager;
	
	public function __construct(Adapter $adapter, ServiceManager $serviceManager) {
		// Set DB adapter
		$this->adapter = $adapter;
		
		// Set service manager
		$this->serviceManager = $serviceManager;
	}
	
	public function createTown($longitude, $latitude, $owner) {
		// Insert new row in the table
		$townID = $this->insert(array(
			'longitude' => $longitude,
			'latitude' => $latitude,
			'owner' => $owner
		));
		
		// Start the basic town building project in the new town
		$townbuildingTable = $this->serviceManager->get('Game\Models\TownbuildingTable');
		$townbuildingTable->createTownBuilding($townID, 'town');
	}

	public function getTown($longitude, $latitude) {
		$rowset = $this->select(array(
			'longitude' => $longitude,
			'latitude' => $latitude
		));
		
		$row = $rowset->current();
		
		if($row) {
			return $row;
		}
		return false;
	}

	public function playerOwnsTown($playerID) {
		$rowset = $this->select(array('owner' => $playerID));
		
		if($rowset->current()) {
			return true;
		}
		return false;
	}

	public function reduceAvailableResources($resourceType, $townID) {
		$this->update(array(
			$resourceType => new Expression("($resourceType - 1)")
		), array(
			'townID' => $townID
		));
	}
}

?>