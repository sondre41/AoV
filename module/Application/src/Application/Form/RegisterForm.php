<?php

namespace Application\Form;

use Zend\Form\Form;

class RegisterForm extends Form {
	public function __construct() {
		parent::__construct('register');
		
		$this->setAttribute('method', 'POST');
		
		// E-mail
		$this->add(array(
			'name' => 'email',
			'attributes' => array(
				'type' => 'text'
			),
			'options' => array(
				'label' => 'E-post'
			)
		));
		
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
		
		// Re-type password
		$this->add(array(
			'name' => 'passwordTwo',
			'attributes' => array(
				'type' => 'password'
			),
			'options' => array(
				'label' => 'Skriv inn passord på nytt'
			)
		));
		
		// Submit
		$this->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'value' => 'Registrer',
			)
		));
	}
}

?>