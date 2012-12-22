<?php

namespace Game\Form;

use Zend\Form\Form;

class GuildInviteForm extends Form {
	public function __construct() {
		parent::__construct('guild_invite_player');
		
		$this->setAttribute('method', 'POST');
		
		// Username
		$this->add(array(
			'name' => 'username',
			'attributes' => array(
				'type' => 'text'
			),
			'options' => array(
				'label' => 'Brukernavn'
			)
		));
		
		// Message
		$this->add(array(
			'name' => 'message',
			'attributes' => array(
				'type' => 'textarea',
				'cols' => 50,
				'rows' => 5
			),
			'options' => array(
				'label' => 'Meldingstekst'
			)
		));
		
		// Submit
		$this->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'value' => 'Inviter'
			)
		));
	}
}

?>