<?php

namespace Town\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class TownInventoryModel {
	protected $adapter;
	protected $tableGateway;
	
	public function __construct(Adapter $adapter, TableGateway $tableGateway) {
		$this->adapter = $adapter;
		$this->tableGateway = $tableGateway;
	}
	
	public function delete($townInventoryID) {
		return $this->tableGateway->delete(array('townInventoryID' => $townInventoryID));
	}
	
	public function getPlayerTownInventory($playerID, $townID) {
		// Build SELECT statement
		$sql = new Sql($this->adapter);
		$select = $sql->select('towninventory');
		$select->columns(array('townInventoryID'))
			   ->join('item', 'townInventory.itemID = item.itemID')
			   ->order('bodyPosition DESC');
		
		// Build WHERE statement
		$where = new Where();
		$where->equalTo('playerID', $playerID)
			  ->equalTo('townID', $townID);
		
		// Add WHERE to SELECT
		$select->where($where);
		
		// Execute query
		$sqlString = $sql->getSqlStringForSqlObject($select);
		return $this->adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE);
	}
	
	public function getTownInventoryItem($townInventoryID) {
		// Build SELECT statement
		$sql = new Sql($this->adapter);
		$select = $sql->select('towninventory');
		$select->join('item', 'towninventory.itemID = item.itemID');
		
		// Build WHERE statement
		$where = new Where();
		$where->equalTo('townInventoryID', $townInventoryID);
		
		// Add WHERE to SELECT
		$select->where($where);
		
		// Execute query
		$sqlString = $sql->getSqlStringForSqlObject($select);
		$rowset =  $this->adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE);
		
		$row = $rowset->current();
		
		if($row) {
			return $row;
		}
		return false;
	}
	
	public function insert($itemID, $playerID, $townID) {
		$this->tableGateway->insert(array(
				'itemID' => $itemID,
				'playerID' => $playerID,
				'townID' => $townID
		));
	}
}

?>