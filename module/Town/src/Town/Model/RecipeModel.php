<?php

namespace Town\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class RecipeModel {
	protected $adapter;
	protected $recipeTableGateway;
	protected $playerRecipeTableGateway;

	public function __construct(Adapter $adapter, TableGateway $recipeTableGateway, TableGateway $playerRecipeTableGateway) {
		$this->adapter = $adapter;
		$this->recipeTableGateway = $recipeTableGateway;
		$this->playerRecipeTableGateway = $playerRecipeTableGateway;
	}
	
	public function getRecipe($recipeID) {
		return $this->recipeTableGateway->select(array('recipeID' => $recipeID))->current();
	}
	
	public function getRecipesForPlayer($playerID, $type = null) {
		// Build SELECT statement
		$sql = new Sql($this->adapter);
		$select = $sql->select('playerrecipe');
		$select->join('recipe', 'playerrecipe.recipeID = recipe.recipeID')
			   ->join('item', 'recipe.itemID = item.itemID');
		
		// Build WHERE statement
		$where = new Where();
		$where->equalTo('playerID', $playerID);
		
		if(!is_null($type)) {
			$where->equalTo('recipe.type', $type);
		}
		
		// Add WHERE to SELECT
		$select->where($where);
		
		// Execute query
		$sqlString = $sql->getSqlStringForSqlObject($select);
		return $this->adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE);
	}

	public function playerHasRecipe($playerID, $recipeID) {
		$rowset = $this->playerRecipeTableGateway->select(array(
			'playerID' => $playerID,
			'recipeID' => $recipeID
		));
		
		$row = $rowset->current();
		if($row) {
			return true;
		}
		return false;
	}
}

?>