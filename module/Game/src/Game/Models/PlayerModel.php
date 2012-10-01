<?php

namespace Game\Models;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class PlayerModel {
	protected $adapter;
	
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
	
	public function getPlayerActiveItems($playerID) {
		// Build SELECT statement
		$sql = new Sql($this->adapter);
		$select = $sql->select('player');
		$select->columns(array('head', ''));
	}
}

?>