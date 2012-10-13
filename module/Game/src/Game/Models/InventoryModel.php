<?php

namespace Game\Models;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class InventoryModel {
	protected $adapter;
	
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
	
	// Activates a specific inventory item for a specific player
	public function activateItemForPlayer($inventoryID, $playerID, $hand = null) {
		// Get the specific inventory item by ID
		$item = $this->getInventoryItem($inventoryID);
		
		// Check for item steal attempt
		if($item->playerID != $playerID) {
			// The user has tried to steal an item. But we KNOW IT, and they're gonna pay for it by being reported, MWAHAHAHAH!!!
		}
		
		// Deactivate the current inventory item in the given body slot, if any
		$bodySlot = $item->bodyPosition;
		if($bodySlot == 'hand') {
			if($hand == 'left') {
				$bodySlot = 'leftHand';
			} else {
				$bodySlot = 'rightHand';
			}
		}
		
		$this->deactivateBodySlotForPlayer($bodySlot, $playerID);
		
		// Activate the new item
		$affectedRows = $this->activateBodySlotWithInventoryItemForPlayer($bodySlot, $inventoryID, $playerID);
		
		if($affectedRows == 0) {
			return false;
		}
		return true;
	}
	
	// Get all items in the inventory of a specific player
	public function getPlayerInventory($playerID) {
		// Build SELECT statement
		$sql = new Sql($this->adapter);
		$select = $sql->select('inventory');
		$select->columns(array('inventoryID', 'bodySlot'))
			   ->join('item', 'inventory.itemID = item.itemID')
			   ->order('bodyPosition DESC');
		
		// Build WHERE statement
		$where = new Where();
		$where->equalTo('playerID', $playerID);
		
		// Add WHERE to SELECT
		$select->where($where);
		
		// Execute query
		$sqlString = $sql->getSqlStringForSqlObject($select);
		return $this->adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE);
	}
	
	// Returns whether or not a specific player has a specific item active in his inventory
	public function playerHasItemTypeActive($playerID, $itemType) {
		// Build SELECT statement
		$sql = new Sql($this->adapter);
		$select = $sql->select('inventory');
		$select->columns(array('inventoryID'))
			   ->join('item', 'inventory.itemID = item.itemID', array());
		
		// Build WHERE statement
		$where = new Where();
		$where->equalTo('playerID', $playerID)
			  ->equalTo('item.type', $itemType)
			  ->in('bodySlot', array('rightHand', 'leftHand'));
		
		// Add WHERE to SELECT
		$select->where($where);
		
		// Execute query
		$sqlString = $sql->getSqlStringForSqlObject($select);
		$resultSet = $this->adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE);
		
		if($resultSet->count() > 0) {
			// Item found and active
			return true;
		}
		// Item not found or not active
		return false;
	}
	
	// Get a specific inventory item
	public function getInventoryItem($inventoryID) {
		// Build SELECT statement
		$sql = new Sql($this->adapter);
		$select = $sql->select('inventory');
		$select->join('item', 'inventory.itemID = item.itemID');
		
		// Build WHERE statement
		$where = new Where();
		$where->equalTo('inventoryID', $inventoryID);
		
		// Add WHERE to SELECT
		$select->where($where);
		
		// Execute query
		$sqlString = $sql->getSqlStringForSqlObject($select);
		$resultSet =  $this->adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE);
		return $resultSet->current();
	}
	
	// Activate a body slot with a specific inventory item for a specific player
	public function activateBodySlotWithInventoryItemForPlayer($bodySlot, $inventoryID, $playerID) {
		// Build UPDATE statement
		$sql = new Sql($this->adapter);
		$update = $sql->update('inventory');
		$update->set(array('bodySlot' => $bodySlot));
		
		// Build WHERE statement
		$where = new Where();
		$where->equalTo('inventoryID', $inventoryID)
			  ->equalTo('playerID', $playerID);
		
		// Add WHERE to UPDATE
		$update->where($where);
		
		// Execute query
		$sqlString = $sql->getSqlStringForSqlObject($update);
		$resultSet = $this->adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE);
		return $resultSet->count();
	}
	
	// Deactivate a body slot for a specific player
	public function deactivateBodySlotForPlayer($bodySlot, $playerID) {
		// Build UPDATE statement
		$sql = new Sql($this->adapter);
		$update = $sql->update('inventory');
		$update->set(array('bodySlot' => null));
		
		// Build WHERE statement
		$where = new Where();
		$where->equalTo('bodySlot', $bodySlot)
			  ->equalTo('playerID', $playerID);
		
		// Add WHERE to UPDATE
		$update->where($where);
		
		// Execute query
		$sqlString = $sql->getSqlStringForSqlObject($update);
		$resultSet = $this->adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE);
		$affectedRows = $resultSet->count();
		
		if($affectedRows == 0) {
			return false;
		}
		return true;
	}
}

?>