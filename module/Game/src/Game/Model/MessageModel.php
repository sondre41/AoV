<?php

namespace Game\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;

class MessageModel {
	protected $adapter;
	protected $messageTableGateway;

	public function __construct(Adapter $adapter, TableGateway $messageTableGateway) {
		$this->adapter = $adapter;
		$this->messageTableGateway = $messageTableGateway;
	}
	
	public function createMessage($recipient, $sender, $topic, $content) {
		$this->messageTableGateway->insert(array(
			'recipient' => $recipient,
			'sender' => $sender,
			'topic' => $topic,
			'content' => $content
		));
	}
	
	public function deleteMessage($messageId) {
		return $this->messageTableGateway->delete(array('messageID' => $messageId));
	}
	
	public function getMessage($messageId) {
		$rowset = $this->messageTableGateway->select(array('messageID' => $messageId));
		
		$row = $rowset->current();
		if(! $row) {
			throw new \Exception("The message with an ID of: $messageId could not be found.");
		}
		return $row;
	}
	
	public function getMessagesForPlayer($playerId) {
		return $this->messageTableGateway->select(array('recipient' => $playerId));
	}
}