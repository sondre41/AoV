<?php

namespace Town\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class TownBuildingTable extends AbstractTableGateway {
	protected $table = 'townbuilding';
	
	public function __construct(Adapter $adapter) {
		// Set DB adapter
		$this->adapter = $adapter;
	}
	
	public function createTownBuilding($townID, $type) {
		// Set tree and stone requirements for the given building type
		switch($type) {
			case 'town':
				$lumber = 20;
				$stone = 15;
				$log = 0;
				break;
			case 'resource storage':
				$lumber = 30;
				$stone = 10;
				$log = 0;
				break;
			case 'item storage':
				$lumber = 50;
				$stone = 20;
				$log = 0;
				break;
			case 'blacksmith':
				$lumber =  40;
				$stone = 10;
				$log = 0;
				break;
			case 'saw buck':
				$lumber = 0;
				$stone = 4;
				$log = 10;
				break;
			case 'lumbermill':
				$lumber = 20;
				$stone = 5;
				$log = 5;
				break;
			case 'barracks':
				$lumber = 10;
				$stone = 40;
				$log = 10;
				break;
			case 'tailor':
				$lumber = 30;
				$stone = 15;
				$log = 3;
				break;
			case 'iron melting works':
				$lumber = 20;
				$stone = 10;
				$log = 5;
				break;
			case 'steel melting works':
				$lumber = 30;
				$stone = 20;
				$log = 3;
				break;
			case 'gold melting works':
				$lumber = 40;
				$stone = 30;
				$log = 15;
				break;
		}
		
		// Insert a new row in the table
		$this->insert(array(
			'townID' => $townID,
			'type' => $type,
			'lumberLeft' => $lumber,
			'stoneLeft' => $stone,
			'logLeft' => $log
		));
	}
	
	public function getAllBuildings() {
		return array(
			'town',
			'resource storage',
			'item storage',
			'blacksmith',
			'saw buck',
			'lumbermill',
			'barracks',
			'tailor',
			'iron melting works',
			'steel melting works',
			'gold melting works'
		);
	}
	
	public function getBuildingprerequisites() {
		return array(
			'town' => null,
			'resource storage' => null,
			'item storage' => array(
				'resource storage'
			),
			'tailor' => null,
			'blacksmith' => array(
				'tailor'
			),
			'saw buck' => null,
			'lumbermill' => array(
				'saw buck'
			),
			'barracks' => null,
			'iron melting works' => null,
			'gold melting works' => array(
				'iron melting works'
			),
			'steel melting works' => array(
				'iron melting works',
				'gold melting works'
			)
		);
	}
	
	public function getBuildingsWithPrerequisitesObtained($completedBuildings = null, $townID = null) {
		if(is_null($completedBuildings) && is_null($townID)) {
			throw new \Exception("Either the completedBuildings ResultSet nor the townID paramter was not set.");
		}
		
		if(is_null($completedBuildings)) {
			$completedBuildings = $this->getCompletedTownBuildingsForTown($townID);
		}

		$buildings = $this->getAllBuildings();
		$buildingPrerequsites = $this->getBuildingprerequisites();
		
		foreach($buildings as $building) {
			if(is_null($buildingPrerequsites[$building])) {
				$returnArray[] = $building;
			} else {
				foreach($buildingPrerequsites[$building] as $prerequisite) {
					$allPrerequisitesObtained = true;
					if(!in_array($prerequisite, $completedBuildings)) {
						$allPrerequisitesObtained = false;
					}
				}
				
				if($allPrerequisitesObtained) {
					$returnArray[] = $building;
				}
			}
		}
		
		return $returnArray;
	}
	
	public function getCompletedTownBuildingsForTown($townID) {
		// Build SELECT statement
		$sql = new Sql($this->adapter);
		$select = $sql->select($this->table);
		$select->columns(array('type'))
			   ->where(array(
			   		'townID' => $townID,
			   		'lumberLeft' => 0,
			   		'stoneLeft' => 0,
			   		'logLeft' => 0
			   	));
		
		// Execute query
		$sqlString = $sql->getSqlStringForSqlObject($select);
		$result = $this->adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE);
		
		foreach($result as $building) {
			$returnArray[] = $building->type;
		}
		
		return $returnArray;
	}
	
	public function getUncompletedTownBuildingForTown($townID) {
		// Build WHERE statement
		$where = new Where();
		$where->equalTo('townID', $townID);
		
		// Make sub-WHERE statement to be able to OR two statements together
		$resourceWhere = new Where(null, Where::COMBINED_BY_OR);
		$resourceWhere->notEqualTo('lumberLeft', 0)
					  ->notEqualTo('stoneLeft', 0)
					  ->notEqualTo('logLeft', 0);
		
		$where->addPredicate($resourceWhere);
		
		// Conduct the SELECT query
		$rowset = $this->select($where);
		
		// Check whether or not a row was found
		$row = $rowset->current();
		if($row) {
			return $row;
		}
		return false;
	}

	public function reduceResourceLeftForBuilding($resourceType, $townID) {
		// Build WHERE statement
		$where = new Where();
		$where->equalTo('townID', $townID);
		
		// Make sub-WHERE statement to be able to OR two statements together
		$resourceWhere = new Where(null, Where::COMBINED_BY_OR);
		$resourceWhere->notEqualTo('lumberLeft', 0)
		->notEqualTo('stoneLeft', 0);
		
		$where->addPredicate($resourceWhere);
		
		// Conduct the UPDATE query
		$this->update(array(
			$resourceType . 'Left' => new Expression("(" . $resourceType . "Left - 1)")
		), $where);
	}
	
	public function stopBuildingProject($townID) {
		// Build WHERE statement
		$where = new Where();
		$where->equalTo('townID', $townID);
		
		// Make sub-WHERE statement to be able to OR two statements together
		$resourceWhere = new Where(null, Where::COMBINED_BY_OR);
		$resourceWhere->notEqualTo('lumberLeft', 0)
					  ->notEqualTo('stoneLeft', 0)
					  ->notEqualTo('logLeft', 0);
		
		$where->addPredicate($resourceWhere);
		
		// Conduct DELETE statement
		$this->delete($where);
	}
	
	public function townHasBuilding($buildingType, $townID) {
		// Conduct SELECT statement
		$rowset = $this->select(array(
				'townID' => $townID,
				'type' => $buildingType,
				'lumberLeft' => 0,
				'stoneLeft' => 0
		));
		
		$row = $rowset->current();
		
		if($row) {
			return true;
		}
		return false;
	}
}

?>