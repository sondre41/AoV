<?php

namespace Application\Form\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Validator;

class AuthFormInputFilter extends InputFilter {
	public function __construct() {
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
	}
}

?>