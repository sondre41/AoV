<?php

namespace Game\Models;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;

class InventoryTable extends AbstractTableGateway {
	protected $table ='inventory';
	
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
		
		$this->resultSetPrototype = new ResultSet();
		$this->resultSetPrototype->setArrayObjectPrototype(new InventoryItem());
		
		$this->initialize();
	}
	
	public function giveItemToPlayer($itemID, $playerID) {
		$this->insert(array('itemID' => $itemID,
							'playerID' => $playerID));
	}
	
	public function deleteItemForPlayer($inventoryID, $playerID) {
		// Build WHERE clause
		$where = new Where();
		$where->equalTo('playerID', $playerID)
			  ->equalTo('inventoryID', $inventoryID);
		
		// Delete
		return $this->delete($where);
	}
}

?>