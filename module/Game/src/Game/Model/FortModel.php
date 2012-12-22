<?php

namespace Game\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class FortModel {
	protected $adapter;
	protected $fortTableGateway;
	protected $fortFightTableGateway;
	protected $fortFightInvitationTableGateway;
	
	public function __construct(Adapter $adapter, TableGateway $fortTableGateway, TableGateway $fortFightTableGateway, TableGateway $fortFightInvitationTableGateway) {
		// Set DB adapter
		$this->adapter = $adapter;
		
		// Set DB table gateways
		$this->fortTableGateway = $fortTableGateway;
		$this->fortFightTableGateway = $fortFightTableGateway;
		$this->fortFightInvitationTableGateway = $fortFightInvitationTableGateway;
	}
	
	public function getFort($fortId) {
		// Build SELECT statement
		$sql = new Sql($this->adapter);
		$select = $sql->select('fort');
		$select->join('guild', 'guild.guildID = fort.guildInControl', Select::SQL_STAR, Select::JOIN_LEFT)
			   ->where(array('fortID' => $fortId));
		
		// Execute query
		$sqlString = $sql->getSqlStringForSqlObject($select);
		$rowset = $this->adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE);
		
		$row = $rowset->current();
		if($row) {
			return $row;
		}
		return false;
	}
	
	public function getForts() {
		// Build SELECT statement
		$sql = new Sql($this->adapter);
		$select = $sql->select('fort');
		$select->join('guild', 'guild.guildID = fort.guildInControl', Select::SQL_STAR, Select::JOIN_LEFT);
		
		// Execute query
		$sqlString = $sql->getSqlStringForSqlObject($select);
		return $this->adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE);
	}
	
	public function createFortFight($guildId, $fortId) {
		$this->fortFightTableGateway->insert(array(
			'guildID' => $guildId,
			'fortID' => $fortId
		));
		
		return $this->fortFightTableGateway->getLastInsertValue();
	}
	
	public function deleteFortFightInvite($fortFightId, $playerId) {
		return $this->fortFightInvitationTableGateway->delete(array(
			'fortFightID' => $fortFightId,
			'playerID' => $playerId
		));
	}
	
	public function getFortFight($fortFightId) {
		// Build SELECT statement
		$sql = new Sql($this->adapter);
		$select = $sql->select('fortfight');
		$select->join('guild', 'guild.guildID = fortfight.guildID')
			   ->where(array('fortFightID' => $fortFightId));
		
		// Execute query
		$sqlString = $sql->getSqlStringForSqlObject($select);
		$rowset = $this->adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE);
		
		$row = $rowset->current();
		if($row) {
			return $row;
		}
		return false;
	}
	
	public function getFortFightsForGuild($guildId) {
		return $this->fortFightTableGateway->select(array(
			'guildID' => $guildId
		));
	}
	
	public function getFortFightInvitationsForFortFight($fortFightId) {
		// Build SELECT statement
		$sql = new Sql($this->adapter);
		$select = $sql->select('fortfightinvitation');
		$select->join('player', 'player.playerID = fortfightinvitation.playerID')
			   ->order('status');
		
		// Execute query
		$sqlString = $sql->getSqlStringForSqlObject($select);
		return $this->adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE);
	}
	
	public function invitePlayerToFortFight($playerId, $fortFightId) {
		$this->fortFightInvitationTableGateway->insert(array(
			'playerID' => $playerId,
			'fortFightID' => $fortFightId
		));
	}
	
	public function setFortFightTime($fortFightId) {
		$this->fortFightTableGateway->update(array(
			'time' => date('YmdHis', time() + 60 * 60 * 24)
		), array(
			'fortFightID' => $fortFightId
		));
	}
}

?>