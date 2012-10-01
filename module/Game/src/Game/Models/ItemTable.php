<?php

namespace Game\Models;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql;

class ItemTable extends AbstractTableGateway {
	protected $table ='item';
	
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
		$this->resultSetPrototype = new ResultSet();
		$this->resultSetPrototype->setArrayObjectPrototype(new Item());
		$this->initialize();
	}
	
	public function getItem($itemName) {
		$rowset = $this->select(array('name' => $itemName));
		$row = $rowset->current();
		if(!$row) {
			throw new \Exception("Item with name: $itemName, could not be found.");
		}
		
		return $row;
	}
}

?>