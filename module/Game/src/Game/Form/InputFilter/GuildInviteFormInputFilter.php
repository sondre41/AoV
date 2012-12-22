<?php

namespace Game\Form\InputFilter;

use Zend\Db\Adapter\Adapter;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Validator;

class GuildInviteFormInputFilter extends InputFilter {
	public function __construct(Adapter $databaseAdapter) {		
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
					'name' => 'db\record_exists',
					'options' => array(
						'adapter' => $databaseAdapter,
						'table' => 'player',
						'field' => 'username'
					)
				)
			)
		));
		
		// Message
		$this->add(array(
			'name' => 'message',
			'required' => false,
			'validators' => array(
				array(
					'name' => 'string_length',
					'options' => array(
						'max' => 500
					)
				)
			)
		));
	}
}

?>