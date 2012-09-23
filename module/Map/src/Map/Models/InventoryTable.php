<?php

namespace Map\Models;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql;

class InventoryTable extends AbstractTableGateway {
	protected $table ='inventory';
	
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
		$this->resultSetPrototype = new ResultSet();
		$this->resultSetPrototype->setArrayObjectPrototype(new Inventory());
		$this->initialize();
	}
	
	public function giveItemToPlayer($itemID, $playerID) {
		$this->insert(array('itemID' => $itemID,
							'playerID' => $playerID));
	}
}

?>