<?php

namespace Game\Model;

use Zend\Db\TableGateway\AbstractTableGateway;

class CityTable extends AbstractTableGateway {
	protected $adapter;
	
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
		$this->initialize();
	}
	
	public function fetchAll() {
		return $this->select();
	}
}

?>