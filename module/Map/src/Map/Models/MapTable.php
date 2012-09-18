<?php

namespace Map\Models;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class MapTable extends AbstractTableGateway
{
	protected $table ='map';
	
	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
		$this->resultSetPrototype = new ResultSet();
		$this->resultSetPrototype->setArrayObjectPrototype(new MapSquare());
		$this->initialize();
	}
	
	public function fetchAll() {
		return $this->select();
	}
	
	public function getMapPart($longitude = 0, $latitude = 0) {
		$where =  "longitude >= $longitude AND ";
		$where .= "longitude < " . ($longitude + 4) . " AND ";
		$where .= "latitude >= $latitude AND ";
		$where .= "latitude < " . ($latitude + 4);
		
		return $this->select($where);
	}
}

?>