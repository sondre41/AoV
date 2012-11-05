<?php

namespace Game\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class InventoryTable extends AbstractTableGateway {
	protected $table ='inventory';
	
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
		
		$this->resultSetPrototype = new ResultSet();
		$this->resultSetPrototype->setArrayObjectPrototype(new InventoryItem());
	}
	
	public function giveItemToPlayer($itemID, $playerID) {
		$this->insert(array(
			'itemID' => $itemID,
			'playerID' => $playerID
		));
	}
	
	public function deleteItemForPlayer($inventoryID, $playerID) {
		return $this->delete(array(
			'playerID' => $playerID,
			'inventoryID' => $inventoryID
		));
	}
}

?>