<?php

namespace Game\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceManager;

class QuestModel {
	protected $adapter;
	protected $questTableGateway;
	protected $playerQuestTableGateway;
	protected $serviceManager;
	
	public function __construct(Adapter $adapter, TableGateway $questTableGateway, TableGateway $playerQuestTableGateway, ServiceManager $serviceManager) {
		$this->adapter = $adapter;
		$this->questTableGateway = $questTableGateway;
		$this->playerQuestTableGateway = $playerQuestTableGateway;
		$this->serviceManager = $serviceManager;
	}
	
	public function checkForPlayerMissionsCompleted($playerID) {
		$activeQuests = $this->getActivePlayerQuests($playerID);
		
		foreach($activeQuests as $quest) {
			$questClassName = 'Game\Quest\\' . str_replace(' ', '', $quest->title);
			
			$questClass = new $questClassName($this->serviceManager);
			$questClass->checkPrerequisites();
			
			if($questClass->isCompleted()) {
				$this->completePlayerQuest($playerID, $quest->questID);
			}
		}
	}
	
	public function completePlayerQuest($playerID, $questID) {
		// Build UPDATE statement
		$sql = new Sql($this->adapter);
		$update = $sql->update('playerquest');
		$update->set(array('timeCompleted' => date('YmdHis', time())));
		
		// Build WHERE statement
		$where = new Where();
		$where->equalTo('playerID', $playerID)
			  ->equalTo('questID', $questID);
		
		// Add WHERE to UPDATE
		$update->where($where);
		
		// Execute query
		$sqlString = $sql->getSqlStringForSqlObject($update);
		$this->adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE);
	}
	
	public function getActivePlayerQuests($playerID) {
		// Build SELECT statement
		$sql = new Sql($this->adapter);
		$select = $sql->select('playerquest');
		$select->join('quest', 'playerquest.questID = quest.questID');
		
		// Build WHERE statement
		$where = new Where();
		$where->equalTo('playerID', $playerID)
			  ->isNull('timeCompleted');
		
		// Add WHERE to SELECT
		$select->where($where);
		
		// Execute query
		$sqlString = $sql->getSqlStringForSqlObject($select);
		return $this->adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE);
	}
	
	public function getAllPlayerQuests($playerID) {
		// Build SELECT statement
		$sql = new Sql($this->adapter);
		$select = $sql->select('playerquest');
		$select->join('quest', 'playerquest.questID = quest.questID')
			   ->order(array('timeCompleted ASC', 'timeStarted ASC'));
	
		// Build WHERE statement
		$where = new Where();
		$where->equalTo('playerID', $playerID);
	
		// Add WHERE to SELECT
		$select->where($where);
	
		// Execute query
		$sqlString = $sql->getSqlStringForSqlObject($select);
		return $this->adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE);
	}
}

?>