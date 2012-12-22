<?php

namespace Application\Form\InputFilter;

use Zend\Db\Adapter\Adapter;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Validator;

class RegisterFormInputFilter extends InputFilter {
	public function __construct(Adapter $databaseAdapter) {
		// E-mail
		$this->add(array(
			'name' => 'email',
			'required' => true,
			'validators' => array(
				array(
					'name' => 'email_address',
					'break_chain_on_failure' => true
				),
				array(
					'name' => 'string_length',
					'options' => array(
						'min' => 5,
						'max' => 100
					),
					'break_chain_on_failure' => true
				),
				array(
					'name' => 'db\no_record_exists',
					'options' => array(
						'adapter' => $databaseAdapter,
						'table' => 'player',
						'field' => 'email'
					)
				)
			)
		));
		
		// Username
		$this->add(array(
			'name' => 'username',
			'required' => true,
			'validators' => array(
				array(
					'name' => 'not_empty',
					'break_chain_on_failure' => true
				),
				array(
					'name' => 'string_length',
					'options' => array(
						'min' => 5,
						'max' => 30
					),
					'break_chain_on_failure' => true
				),
				array(
					'name' => 'db\no_record_exists',
					'options' => array(
							'adapter' => $databaseAdapter,
							'table' => 'player',
							'field' => 'username'
					)
				)
			)
		));
		
		// Password
		$this->add(array(
			'name' => 'password',
			'required' => true,
			'validators' => array(
				array(
					'name' => 'not_empty',
					'break_chain_on_failure' => true
				),
				array(
					'name' => 'string_length',
					'options' => array(
						'min' => 5,
						'max' => 30
					)
				)
			)
		));
		
		// Re-type password
		$this->add(array(
			'name' => 'passwordTwo',
			'required' => true,
			'validators' => array(
				array(
					'name' => 'identical',
					'options' => array(
						'token' => 'password'
					)
				)
			)
		));
	}
}

?>