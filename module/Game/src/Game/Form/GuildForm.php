<?php

namespace Game\Form;

use Zend\Form\Form;

class GuildForm extends Form {
	public function __construct($edit = false) {
		parent::__construct('guild');
		
		$this->setAttribute('method', 'POST');
		
		if(! $edit) {
			// Name
			$this->add(array(
				'name' => 'name',
				'attributes' => array(
					'type' => 'text'
				),
				'options' => array(
					'label' => 'Navn'
				)
			));
		}
		
		// Description
		$this->add(array(
			'name' => 'description',
			'attributes' => array(
				'type' => 'textarea',
				'cols' => 50,
				'rows' => 5
			),
			'options' => array(
				'label' => 'Beskrivelse'
			)
		));
		
		// Submit
		$this->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit'
			)
		));
	}
}

?>