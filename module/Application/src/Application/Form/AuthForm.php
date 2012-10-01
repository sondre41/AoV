<?php

namespace Application\Form;

use Zend\Form\Form;

class AuthForm extends Form {
	public function __construct() {
		parent::__construct('auth');
		
		$this->setAttribute('method', 'post');
		
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
		
		// Password
		$this->add(array(
				'name' => 'password',
				'attributes' => array(
						'type' => 'password'
				),
				'options' => array(
						'label' => 'Passord'
				)
		));
		
		// Submit
		$this->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'value' => 'Logg inn',
			)
		));
	}
}

?>