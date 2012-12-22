<?php

namespace Game\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class GuildModel {
	protected $adapter;
	protected $guildInvitationTableGateway;
	protected $guildTableGateway;
	
	public function __construct(Adapter $adapter, TableGateway $guildInvitationTableGateway, TableGateway $guildTableGateway) {
		$this->adapter = $adapter;
		$this->guildInvitationTableGateway = $guildInvitationTableGateway;
		$this->guildTableGateway = $guildTableGateway;
	}
	
	public function getGuildInfo($guildId) {
		// Build SELECT statement
		$sql = new Sql($this->adapter);
		$select = $sql->select('guild');
		$select->join('player', 'guild.owner = player.playerID');
		
		// Build WHERE statement
		$where = new Where();
		$where->equalTo('guild.guildID', $guildId);
		
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
	
	public function getGuildInvitationsForPlayer($playerId) {
		// Build SELECT statement
		$sql = new Sql($this->adapter);
		$select = $sql->select('guildinvitation');
		$select->join('guild', 'guild.guildID = guildinvitation.guildID')
			   ->where('playerID', $playerId);
		
		// Execute query
		$sqlString = $sql->getSqlStringForSqlObject($select);
		return $this->adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE);
	}

	public function createGuild($data, $owner) {
		$this->guildTableGateway->insert(array(
			'owner' => $owner,
			'name' => $data['name'],
			'description' => $data['description']
		));
		
		return $this->guildTableGateway->getLastInsertValue();
	}
	
	public function createGuildInvitation($guildId, $playerId) {
		$this->guildInvitationTableGateway->insert(array(
			'guildID' => $guildId,
			'playerID' => $playerId
		));
	}
	
	public function deleteGuild($guildId) {
		return $this->guildTableGateway->delete(array('guildID' => $guildId));
	}
	
	public function deleteInvitation($playerId, $guildId) {
		return $this->guildInvitationTableGateway->delete(array(
			'playerID' => $playerId,
			'guildID' => $guildId
		));
	}
	
	public function hasPlayerInvitationFromGuild($playerId, $guildId) {
		$rowset = $this->guildInvitationTableGateway->select(array(
			'playerID' => $playerId,
			'guildID' => $guildId
		));
		
		$row = $rowset->current();
		if($row) {
			return true;
		}
		return false;
	}
	
	public function updateGuildInfo($guildId, $data) {
		$this->guildTableGateway->update(array(
			'description' => $data['description']
		), array(
			'guildID' => $guildId
		));
	}
}

?>