<?php

namespace Game\Form\InputFilter;

use Zend\Db\Adapter\Adapter;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Validator;

class GuildFormInputFilter extends InputFilter {
	public function __construct(Adapter $databaseAdapter, $edit = false) {		
		if(! $edit) {
			// Name
			$this->add(array(
				'name' => 'name',
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
						'name' => 'db\no_record_exists',
						'options' => array(
							'adapter' => $databaseAdapter,
							'table' => 'guild',
							'field' => 'name'
						)
					)
				)
			));
		}
		
		// Description
		$this->add(array(
			'name' => 'description',
			'required' => false,
			'validators' => array(
				array(
					'name' => 'string_length',
					'options' => array(
						'max' => 250
					)
				)
			)
		));
	}
}

?>