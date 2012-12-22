<?php

namespace Game\Controller;

class FortController extends GameController {
	public function indexAction() {
		// Get information about all the forts
		$forts = $this->getFortModel()->getForts();
		
		// Check whether or not the player is the owner of a guild
		$player = $this->getPlayerTable()->getPlayer($this->playerId);
		$guild = $this->getGuildModel()->getGuildInfo($player->guildID);
		
		if($player->playerID == $guild->owner) {
			$playerIsGuildOwner = true;
		} else {
			$playerIsGuildOwner = false;
		}
		
		// Get upcoming fortfights for the guild the player is in
		$fortFights = $this->getFortModel()->getFortFightsForGuild($guild->guildID)->toArray();
		
		return array(
			'forts' => $forts,
			'playerIsGuildOwner' => $playerIsGuildOwner,
			'fortFights' => $fortFights
		);
	}
	
	public function attackAction() {
		// Get a required fort id from the request
		$fortId = $this->params()->fromRoute('id', false);
		
		if(! $fortId) {
			return $this->redirect()->toRoute('fort');
		}
		
		// Check whether or not the player is the owner of a guild
		$player = $this->getPlayerTable()->getPlayer($this->playerId);
		$guild = $this->getGuildModel()->getGuildInfo($player->guildID);
		
		if($player->playerID != $guild->owner) {
			return $this->redirect()->toRoute('fort');
		}
		
		// Register the fort fight
		$fortFightId = $this->getFortModel()->createFortFight($guild->guildID, $fortId);
		
		// Redirect to the fort fight page for the new fort fight
		return $this->redirect()->toRoute('fort', array(
			'action' => 'fort-fight',
			'id' => $fortFightId
		));
	}
	
	public function deleteFortFightInviteAction() {
		// Get a required fort fight id and a required player id from the request
		$fortFightId = $this->params()->fromRoute('id', false);
		$playerId = $this->params()->fromRoute('playerId', false);
		
		if(! $fortFightId || ! $playerId) {
			return $this->redirect()->toRoute('fort');
		}
		
		// Get the guild of the player whos invite is to be deleted
		$player = $this->getPlayerTable()->getPlayer($playerId);
		$guild = $this->getGuildModel()->getGuildInfo($player->guildID);
		
		// Get the fort fight
		$fortFight = $this->getFortModel()->getFortFight($fortFightId);
		
		// Get the fort
		$fort = $this->getFortModel()->getFort($fortFight->fortID);
		
		// Check that the guild of the player is either the attacking or the defending guild
		if($fortFight->guildID != $guild->guildID && $fort->guildID != $guild->guildID) {
			return $this->redirect()->toRoute('fort');
		}
		
		// Check that the player deleting the invite is the owner of the guild
		if($guild->owner != $this->playerId) {
			return $this->redirect()->toRoute('fort');
		}
		
		// Everything is OK. Delete the invite and redirect with message to the player.
		$nrOfDeletedInvites = $this->getFortModel()->deleteFortFightInvite($fortFightId, $playerId);
		if($nrOfDeletedInvites > 0) {
			$this->flashMessenger()->addMessage('Fortkampinvitasjonen ble slettet.');
		}
		
		return $this->redirect()->toRoute('fort', array(
			'action' => 'fort-fight',
			'id' => $fortFightId
		));
	}
	
	public function fortFightAction() {
		// Get a required fort fight id from the request
		$fortFightId = $this->params()->fromRoute('id', false);
		
		if(! $fortFightId) {
			return $this->redirect()->toRoute('fort');
		}
		
		// Get information about the fort fight
		$fortFight = $this->getFortModel()->getFortFight($fortFightId);
		
		// Get information about the fort to be attacked
		$fort = $this->getFortModel()->getFort($fortFight->fortID);
		
		$displayOwnerSpecificInfo = false;
		if($this->playerId == $fortFight->owner) {
			$displayOwnerSpecificInfo = true;
		}
		
		// Check whether or not the player is in the attacking guild and therefore shall have guild specific info
		$playersInGuild = $this->getPlayerTable()->getPlayersInGuild($fortFight->guildID)->toArray();
		
		$displayGuildSpecificInfo = false;
		foreach($playersInGuild as $player) {
			if($player['playerID'] == $this->playerId) {
				$displayGuildSpecificInfo = true;
				break;
			}
		}
		
		// Get fort fight invitations for this fort fight
		$fortFightInvitations = $this->getFortModel()->getFortFightInvitationsForFortFight($fortFightId)->toArray();
		
		// Get eventual messages in case of redirect
		$messages = $this->getMessageModel()->getMessagesForPlayer($this->playerId);
		
		return array(
			'playerId' => $this->playerId,
			'fortFight' => $fortFight,
			'fort' => $fort,
			'displayOwnerSpecificInfo' => $displayOwnerSpecificInfo,
			'displayGuildSpecificInfo' => $displayGuildSpecificInfo,
			'playersInGuild' => $playersInGuild,
			'fortFightInvitations' => $fortFightInvitations,
			'flashMessages' => $this->flashMessenger()->getMessages()
		);
	}
	
	public function fortFightInviteAction() {
		// Get a required fort fight id and a required player id from the request
		$fortFightId = $this->params()->fromRoute('id', false);
		$playerId = $this->params()->fromRoute('playerId', false);
		
		if(! $fortFightId || ! $playerId) {
			return $this->redirect()->toRoute('fort');
		}
		
		// Get the fort fight
		$fortFight = $this->getFortModel()->getFortFight($fortFightId);
		
		// Get the invited player
		$invitedPlayer = $this->getPlayerTable()->getPlayer($playerId);
		
		// Get the fort
		$fort = $this->getFortModel()->getFort($fortFight->fortID);
		
		// Check that the invited player is actually allowed to be invited
		// Get the fort fight invitations for this fort fight
		$fortFightInvitations = $this->getFortModel()->getFortFightInvitationsForFortFight($fortFight->fortFightID)->toArray();
		
		// Get the members of the attacking and defending guild
		$playersInAttackingGuild = $this->getPlayerTable()->getPlayersInGuild($fortFight->guildID)->toArray();
		$playersInDefendingGuild = $this->getPlayerTable()->getPlayersInGuild($fort->guildID)->toArray();
		
		// Find all the players in the two guilds that has not been invited to the fort fight
		$playersNotInvited = array();
		
		foreach($playersInAttackingGuild as $player) {
			$playerInvited = false;
			foreach($fortFightInvitations as $invitation) {
				if($player['playerID'] == $invitation['playerID']) {
					$playerInvited = true;
					break;
				}
			}
		
			if(! $playerInvited) {
				$playersNotInvited[] = $player;
			}
		}
		
		foreach($playersInDefendingGuild as $player) {
			$playerInvited = false;
			foreach($fortFightInvitations as $invitation) {
				if($player['playerID'] == $invitation['playerID']) {
					$playerInvited = true;
					break;
				}
			}
		
			if(! $playerInvited) {
				$playersNotInvited[] = $player;
			}
		}
		
		// Check if the player has not been invited
		$playerAllowedToBeInvited = false;
		foreach($playersNotInvited as $player) {
			if($invitedPlayer->playerID == $player['playerID']) {
				$playerAllowedToBeInvited = true;
			}
		}
		
		if(! $playerAllowedToBeInvited) {
			return $this->redirect()->toRoute('fort');
		}
		
		// Invite the player to the fort fight
		$this->getFortModel()->invitePlayerToFortFight($invitedPlayer->playerID, $fortFight->fortFightID);
		
		// Redirect with message to the user
		$message = "Spilleren ble invitert til fortkampen.";
		$this->flashMessenger()->addMessage($message);
		
		return $this->redirect()->toRoute('fort', array(
			'action' => 'fort-fight',
			'id' => $fortFight->fortFightID
		));
	}
	
	public function initiateFortFightAction() {
		// Get a required fort fight id from the request
		$fortFightId = $this->params()->fromRoute('id', false);
		
		if(! $fortFightId) {
			return $this->redirect()->toRoute('fort');
		}
		
		// Check that the player is the owner of the attacking guild
		$fortFight = $this->getFortModel()->getFortFight($fortFightId);
		$guild = $this->getGuildModel()->getGuildInfo($fortFight->guildID);
		
		if($this->playerId != $guild->playerID) {
			return $this->redirect()->toRoute('fort');
		}
		
		// Check that the correct amount of players has accepted their fort fight invitation
		$fort = $this->getFortModel()->getFort($fortFight->fortID);
		$fortFightInvitations = $this->getFortModel()->getFortFightInvitationsForFortFight($fortFightId);
		
		switch($fort->size) {
			case 'small':
				$nrOfPlayersNeeded = 3;
				break;
			case 'medium':
				$nrOfPlayersNeeded = 6;
				break;
			case 'large':
				$nrOfPlayersNeeded = 9;
		}
	
		$nrOfPlayersAccepted = 0;
		foreach($fortFightInvitations as $invitation) {
			if($invitation['status'] == 'accepted') {
				$nrOfPlayersAccepted++;
			}
		}
	
		if($nrOfPlayersAccepted != $nrOfPlayersNeeded) {
			return $this->redirect()->toRoute('fort');
		}
		
		// Everything is OK. Set the fort fight time.
		$this->getFortModel()->setFortFightTime($fortFight->fortFightID);
		
		// Redirect and give message to the player
		$this->flashMessenger()->addMessage('Fortkampen ble initsiert. Tid for fortkampen er satt.');
		
		return $this->redirect()->toRoute('fort', array(
			'action' => 'fort-fight',
			'id' => $fortFight->fortFightID
		));
	}
}

?>