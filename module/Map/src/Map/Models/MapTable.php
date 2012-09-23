<?php

namespace Map\Models;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql;

class MapTable extends AbstractTableGateway {
	protected $table ='map';
	
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
		$this->resultSetPrototype = new ResultSet();
		$this->resultSetPrototype->setArrayObjectPrototype(new MapSquare());
		$this->initialize();
	}
	
	public function fetchAll() {
		return $this->select();
	}
	
	public function getMapPart($height = 11, $width = 13, $longitude = 0, $latitude = 0) {
		$where = new Sql\Where();
		$where->greaterThanOrEqualTo('longitude', $longitude)
			  ->greaterThanOrEqualTo('latitude', $latitude)
			  ->lessThan('longitude', $longitude + $height)
			  ->lessThan('latitude', $latitude + $width);
		
		return $this->select($where);
	}
	
	// Get a single map square from DB
	public function getMapSquare($longitude, $latitude) {
		$rowset = $this->select(array('longitude' => $longitude, 'latitude' => $latitude));
		$row = $rowset->current();
		if(!$row) {
			throw new \Exception("Map square with longitude: $longitude, and latitude: $latitude, could not be found.");
		}
		
		return $row;
	}
	
	// Randomly generate a map of the given width and height
	public function randomGenerateMap($height = 100, $width = 100) {
		for($longitude = 0; $longitude < $height; $longitude++) {
			for($latitude = 0; $latitude < $width; $latitude++) {
				$randomNumber = rand(1, 100);
				$type = 0;
				if($randomNumber <= 15) {
					$type = 1;
				} else if($randomNumber <= 25) {
					$type = 2;
				} else if($randomNumber <= 35) {
					$type = 3;
				} else if($randomNumber <= 40) {
					$type = 4;
				}
				
				if($this->mapSquareExists($longitude, $latitude)) {
					$this->update(array('type' => $type), array('longitude' => $longitude, 'latitude' => $latitude));
				} else {
					$this->insert(array('longitude' => $longitude, 'latitude' => $latitude, 'type' => $type));
				}
			}
		}
	}
	
	// Returns whether or not a map square with the given position is represented in the DB
	private function mapSquareExists($longitude, $latitude) {
		$rowset = $this->select(array('longitude' => $longitude,
				'latitude' => $latitude));
		$row = $rowset->current();
		if ($row) {
			return true;
		}
		return false;
	}
}

?>