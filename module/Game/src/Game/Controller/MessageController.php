<?php

namespace Game\Controller;

use Game\Form\MessageForm;
use Game\Form\InputFilter\MessageFormInputFilter;

class MessageController extends GameController {
	public function indexAction() {
		// Get the players messages
		$messages = $this->getMessageModel()->getMessagesForPlayer($this->playerId);
		
		return array(
			'messages' => $messages,
			'flashMessages' => $this->flashMessenger()->getMessages()
		);
	}
	
	public function readAction() {
		$messageId = $this->params()->fromRoute('id', false);
		
		if(! $messageId) {
			return $this->redirect()->toRoute('message');
		}
		
		$message = $this->getMessageModel()->getMessage($messageId);
		
		// Check reading privileges
		if($this->playerId != $message->recipient) {
			return $this->redirect()->toRoute('message');
		}
		
		$sender = $this->getPlayerTable()->getPlayer($message->sender);
		
		return array(
			'message' => $message,
			'sender' => $sender
		);
	}
	
	public function sendAction() {
		$form = new MessageForm();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
			$form->setInputFilter(new MessageFormInputFilter($dbAdapter));
			$form->setData($request->getPost());
				
			if ($form->isValid()) {
				$data = $form->getData();
				
				// Insert the mail in DB
				$recipient = $this->getPlayerTable()->getPlayerByUsername($data['recipient']);
				$this->getMessageModel()->createMessage($recipient->playerID, $this->playerId, $data['topic'], $data['content']);
				
				// Give message to the user and redirect to mail homepage
				$this->flashMessenger()->addMessage('Meldingen ble sendt.');
				return $this->redirect()->toRoute('message');
			}
		}
		
		return array('form' => $form);
	}
	
	public function deleteAction() {
		$messageId = $this->params()->fromRoute('id', false);
		
		if(! $messageId) {
			return $this->redirect()->toRoute('message');
		}
		
		$message = $this->getMessageModel()->getMessage($messageId);
		
		// Check deleting privileges
		if($this->playerId != $message->recipient) {
			return $this->redirect()->toRoute('message');
		}
		
		// Delete the message
		$nrOfDeletedMessages = $this->getMessageModel()->deleteMessage($messageId);
		
		// Give message to the user if the message was actually deleted
		if($nrOfDeletedMessages > 0) {
			$this->flashMessenger()->addMessage('Meldingen ble slettet.');
		}
		
		// Redirect to mail homepage
		return $this->redirect()->toRoute('message');
	}
}

?>