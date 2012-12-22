<?php

namespace Game\Form\InputFilter;

use Zend\Db\Adapter\Adapter;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Validator;

class MessageFormInputFilter extends InputFilter {
	public function __construct(Adapter $databaseAdapter) {		
		// Recipient
		$this->add(array(
			'name' => 'recipient',
			'required' => true,
			'validators' => array(
				array(
					'name' => 'not_empty',
					'break_chain_on_failure' => true
				),
				array(
					'name' => 'string_length',
					'options' => array(
						'min' => 2,
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
		
		// Topic
		$this->add(array(
			'name' => 'topic',
			'required' => true,
			'validators' => array(
				array(
					'name' => 'not_empty',
					'break_chain_on_failure' => true
				),
				array(
					'name' => 'string_length',
					'options' => array(
						'max' => 100
					)
				)
			)
		));
		
		// Content
		$this->add(array(
			'name' => 'content',
			'required' => true
		));
	}
}

?>