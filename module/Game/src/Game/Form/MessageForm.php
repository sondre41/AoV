<?php

namespace Game\Form;

use Zend\Form\Form;

class MessageForm extends Form {
	public function __construct() {
		parent::__construct('message');
		
		$this->setAttribute('method', 'POST');
		
		// Recipient
		$this->add(array(
			'name' => 'recipient',
			'attributes' => array(
				'type' => 'text'
			),
			'options' => array(
				'label' => 'Mottaker'
			)
		));
		
		// Topic
		$this->add(array(
			'name' => 'topic',
			'attributes' => array(
				'type' => 'text'
			),
			'options' => array(
				'label' => 'Emne'
			)
		));
		
		// Content
		$this->add(array(
			'name' => 'content',
			'attributes' => array(
				'type' => 'textarea',
				'cols' => 50,
				'rows' => 5
			),
			'options' => array(
				'label' => 'Meldingsinnhold'
			)
		));
		
		// Submit
		$this->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'value' => 'Send'
			)
		));
	}
}

?>