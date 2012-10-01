<?php

namespace Game\Models;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class Inventory {
	protected $adapter;
	
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
	
	// Get all items in the inventory of a specific player
	public function getPlayerInventory($playerID) {
		// Build select statement
		$sql = new Sql($this->adapter);
		$select = $sql->select('inventory');
		$select->columns(array())
			   ->join('item', 'inventory.itemID = item.itemID');
		
		$where = new Where();
		$where->equalTo('playerID', $playerID);
		
		$select->where($where);
		
		// Execute query
		$selectString = $sql->getSqlStringForSqlObject($select);
		return $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
	}
}

?>