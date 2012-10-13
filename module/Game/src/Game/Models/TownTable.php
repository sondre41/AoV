<?php

namespace Game\Models;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter;

class TownTable extends AbstractTableGateway {
	protected $adapter;
	
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
}

?>