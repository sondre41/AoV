<?php

namespace Town\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\ServiceManager\ServiceManager;

class TownTable {
	protected $tableGateway;
	protected $serviceManager;
	
	public function __construct(TableGateway $tableGateway, ServiceManager $serviceManager) {
		// Set table gateway
		$this->tableGateway = $tableGateway;
		
		// Set service manager
		$this->serviceManager = $serviceManager;
	}
	
	public function createTown($longitude, $latitude, $owner) {
		// Insert new row in the table
		$townID = $this->tableGateway->insert(array(
			'longitude' => $longitude,
			'latitude' => $latitude,
			'owner' => $owner
		));
		
		// Start the basic town building project in the new town
		$townbuildingTable = $this->serviceManager->get('Town\Model\TownbuildingTable');
		$townbuildingTable->createTownBuilding($townID, 'town');
	}

	public function getTown($longitude, $latitude) {
		$rowset = $this->tableGateway->select(array(
			'longitude' => $longitude,
			'latitude' => $latitude
		));
		
		$row = $rowset->current();
		
		if($row) {
			return $row;
		}
		return false;
	}
	
	public function getPlayersDefaultTown($owner) {
		$rowset = $this->tableGateway->select(array(
			'owner' => $owner
		));
		
		if($rowset->count() > 0) {
			if($rowset->count() == 1) {
				return $rowset->current();
			} else {
				foreach($rowset as $row) {
					if($row->default == 1) {
						return $row;
					}
				}
			}
		}
		return false;
	}
	
	public function incrementAvailableResources($resourceType, $townID) {
		$this->tableGateway->update(array(
			$resourceType => new Expression("($resourceType + 1)")
		), array(
			'townID' => $townID
		));
	}

	public function playerOwnsTown($playerID) {
		$rowset = $this->tableGateway->select(array('owner' => $playerID));
		
		if($rowset->current()) {
			return true;
		}
		return false;
	}

	public function reduceAvailableResources($resourceType, $townID) {
		$this->tableGateway->update(array(
			$resourceType => new Expression("($resourceType - 1)")
		), array(
			'townID' => $townID
		));
	}
}

?>