<?php

namespace Game\Controller;

use Game\Form\GuildForm;
use Game\Form\GuildInviteForm;
use Game\Form\InputFilter\GuildFormInputFilter;
use Game\Form\InputFilter\GuildInviteFormInputFilter;

class GuildController extends GameController {
	public function indexAction() {
		// Get guild info if the player is in a guild
		$player = $this->getPlayerTable()->getPlayer($this->playerId);
		
		$guildInfo = null;
		$playersInGuild = null;
		$playerHasOwnerRights = false;
		
		if(! is_null($player->guildID)) {
			// Get info about the guild
			$guildInfo = $this->getGuildModel()->getGuildInfo($player->guildID);
			
			// Get all the players in the guild
			$playersInGuild = $this->getPlayerTable()->getPlayersInGuild($player->guildID);
			
			// Check whether or not the player has privileges as an owner in the guild
			if($this->playerId == $guildInfo->owner) {
				$playerHasOwnerRights = true;
			}
		}
		
		// Get eventual guild invitations
		$guildInvitations = $this->getGuildModel()->getGuildInvitationsForPlayer($this->playerId);
		
		return array(
			'guildInfo' => $guildInfo,
			'playersInGuild' => $playersInGuild,
			'playerHasOwnerRights' => $playerHasOwnerRights,
			'guildInvitations' => $guildInvitations,
			'flashMessages' => $this->flashMessenger()->getMessages()
		);
	}
	
	public function acceptInvitationAction() {
		$guildId = $this->params()->fromRoute('id', false);
		
		if(! $guildId) {
			return $this->redirect()->toRoute('guild');
		}
		
		// Check that the player is not currently in a guild
		$player = $this->getPlayerTable()->getPlayer($this->playerId);
		
		if(! is_null($player->guildID)) {
			return $this->redirect()->toRoute('guild');
		}
		
		// Check that the player actually has an invitation from the given guild
		if(! $this->getGuildModel()->hasPlayerInvitationFromGuild($this->playerId, $guildId)) {
			return $this->redirect()->toRoute('guild');
		}
		
		// Set the guild as the players new guild
		$player->guildID = $guildId;
		$player->save();
		
		// Delete the invitation
		$this->getGuildModel()->deleteInvitation($this->playerId, $guildId);
		
		// Redirect to the guild homepage and give a message to the user
		$this->flashMessenger()->addMessage('Du godtok invitasjonen og er nå medlem av klanen.');
		
		return $this->redirect()->toRoute('guild');
	}
	
	public function createAction() {
		$form = new GuildForm();
		$form->get('submit')->setValue('Opprett');
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
			$form->setInputFilter(new GuildFormInputFilter($dbAdapter));
			$form->setData($request->getPost());
			
			if ($form->isValid()) {
				// Create the guild
				$player = $this->getPlayerTable()->getPlayer($this->player->playerID);
				$guildID = $this->getGuildModel()->createGuild($form->getData(), $player->playerID);
				
				// Set the new guild as the players guild
				$player->guildID = $guildID;
				$player->save();
				
				// Give message to the user and redirect to the guild homepage
				$this->flashMessenger()->addMessage('Klanen ble opprettet.');
				return $this->redirect()->toRoute('guild');
			}
		}
		
		return array('form' => $form);
	}
	
	public function deleteInvitationAction() {
		$guildId = $this->params()->fromRoute('id', false);
		
		if(! $guildId) {
			return $this->redirect()->toRoute('guild');
		}

		// Delete the invitation
		$nrOfDeletedInvitations = $this->getGuildModel()->deleteInvitation($this->playerId, $guildId);
		
		if($nrOfDeletedInvitations > 0) {
			$this->flashMessenger()->addMessage('Du avslo invitasjonen.');
		}
		
		return $this->redirect()->toRoute('guild');
	}
	
	public function editAction() {
		$guildId = $this->params()->fromRoute('id', false);
		
		if(! $guildId) {
			return $this->redirect()->toRoute('guild');
		}
		
		$guildInfo = $this->getGuildModel()->getGuildInfo($guildId);
		
		if($this->playerId != $guildInfo->owner) {
			return $this->redirect()->toRoute('guild');
		}
		
		$form = new GuildForm(true);
		$form->get('submit')->setValue('Endre');
		$form->setData(array(
			'name' => $guildInfo->name,
			'description' => $guildInfo->description
		));
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
			$form->setInputFilter(new GuildFormInputFilter($dbAdapter, true));
			$form->setData($request->getPost());
			
			if ($form->isValid()) {
				// Update the guild info
				$this->getGuildModel()->updateGuildInfo($guildInfo->guildID, $form->getData());
				
				// Give message to the user and redirect to the guild homepage
				$this->flashMessenger()->addMessage('Klanens informasjon ble endret.');
				return $this->redirect()->toRoute('guild');
			}
		}
		
		return array(
			'guildId' => $guildId,
			'guild' => $guildInfo,
			'form' => $form
		);
	}
	
	public function inviteAction() {
		$guildId = $this->params()->fromRoute('id', false);
		
		if(! $guildId) {
			return $this->redirect()->toRoute('guild');
		}
		
		$guildInfo = $this->getGuildModel()->getGuildInfo($guildId);
		
		if($this->playerId != $guildInfo->owner) {
			return $this->redirect()->toRoute('guild');
		}
		
		$form = new GuildInviteForm();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
			$form->setInputFilter(new GuildInviteFormInputFilter($dbAdapter));
			$form->setData($request->getPost());
			
			if ($form->isValid()) {
				$data = $form->getData();
				
				// Check that the player to be invited is not already in the guild
				$playersInGuild = $this->getPlayerTable()->getPlayersInGuild($guildInfo->guildID);
				
				$invitedPlayerInGuild = false;
				foreach($playersInGuild as $playerInGuild) {
					if($playerInGuild->username == $data['username']) {
						$invitedPlayerInGuild = true;
						$form->setMessages(array(
							'username' => array('Denne spilleren er allerede medlem av klanen.')
						));
						break;
					}
				}
				
				if(! $invitedPlayerInGuild) {
					// Check that the player has not already got an invitation from this guild
					$invitedPlayer = $this->getPlayerTable()->getPlayerByUsername($data['username']);
					
					if($this->getGuildModel()->hasPlayerInvitationFromGuild($invitedPlayer->playerID, $guildInfo->guildID)) {
						$form->setMessages(array(
								'username' => array('Denne spilleren har allerede mottatt en invitasjon til å bli med i denne klanen.')
						));
					} else {
						$player = $this->getPlayerTable()->getPlayer($this->playerId);
						
						// Create guild invitation message to the invited player
						$topic = "Invitasjon til klan: " . $guildInfo->name;
						$message = "Dette er en invitasjon til deg om å bli medlem av klanen '" . $guildInfo->name . "'.<br>"
								 . "Invitasjonen er sendt deg av spilleren med brukernavn '" . $player->username . "'<br><hr>"
								 . "<i>Personlig beskjed fra spilleren:</i><br>";
						$message.= $data['message'];
						
						$this->getMessageModel()->createMessage($invitedPlayer->playerID, $player->playerID, $topic, $message);
						
						// Create the invitation/guild joining rights
						$this->getGuildModel()->createGuildInvitation($guildInfo->guildID, $invitedPlayer->playerID);
						
						// Redirect to the guild homepage and give message to the user
						$this->flashMessenger()->addMessage('Spilleren ble invitert til klanen.');
						return $this->redirect()->toRoute('guild');
					}
				}
			}
		}
		
		return array(
			'guildInfo' => $guildInfo,
			'form' => $form
		);
	}
	
	public function leaveAction() {
		$player = $this->getPlayerTable()->getPlayer($this->playerId);
		$guild = $this->getGuildModel()->getGuildInfo($player->guildID);
		
		$playerIsOwner = false;
		if($player->playerID == $guild->owner) {
			$playerIsOwner = true;
		}
		
		$request = $this->getRequest();
		
		if($request->isPost()) {
			// Get answer on "Do you want to leave the guild?" question
			$answer = $request->getPost('leaveGuild', 'no');
			
			if(strtolower($answer) == 'yes') {
				if($playerIsOwner) {
					// Delete the guild
					$this->getGuildModel()->deleteGuild($player->guildID);
						
					// Set the players in the guild as not being memeber of any guild
					$playersInGuild = $this->getPlayerTable()->getPlayersInGuild($player->guildID);
					
					foreach($playersInGuild as $playerInGuild) {
						$playerInGuild->guildID = null;
						$playerInGuild->save();
					}
					
					// Give message to the user
					$this->flashMessenger()->addMessage('Klanen ble slettet.');
				} else {
					// Set the player as not being member of any guild
					$player->guildID = null;
					$player->save();
					
					// Give message to the user
					$this->flashMessenger()->addMessage('Du forlot klanen.');
				}
			}
			
			return $this->redirect()->toRoute('guild');
		}
		
		return array(
			'playerIsOwner' => $playerIsOwner
		);
	}
	
	public function viewAction() {
		$guildId = $this->params()->fromRoute('id', false);
		
		if(! $guildId) {
			return $this->redirect()->toRoute('guild');
		}
		
		// Get info about the guild
		$guildInfo = $this->getGuildModel()->getGuildInfo($guildId);
		
		if(! $guildInfo) {
			return $this->redirect()->toRoute('guild');
		}
		
		// Get all the players in the guild
		$playersInGuild = $this->getPlayerTable()->getPlayersInGuild($guildId);
		
		return array(
			'guildInfo' => $guildInfo,
			'playersInGuild' => $playersInGuild
		);
	}
}

?>