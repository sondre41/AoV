<?php

namespace Application\Controller;

// Zend
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

// Application
use Application\Form\RegisterForm;
use Application\Form\InputFilter\RegisterFormInputFilter;

class RegisterController extends AbstractActionController {
	protected $playerTable;
	
	// Create and display register form
	public function indexAction() {
		return array(
			'form' => new RegisterForm()
		);
	}
	
	public function registerAction() {
		// Validate the form data
		$form = new RegisterForm();
		$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
		$form->setInputFilter(new RegisterFormInputFilter($dbAdapter));
		$form->setData($this->getRequest()->getPost());
		
		if(!$form->isValid()) {
			// Re-render login form with error messages
			$view = new ViewModel(array('form' => $form));
			$view->setTemplate('application/register/index');
			return $view;
		} else {
			// Register the new player in the DB
			$this->getPlayerTable()->createPlayer($form->getData());
		}
	}
	
	
	protected function getPlayerTable() {
		if (!$this->playerTable) {
			$serviceManager = $this->getServiceLocator();
			$this->playerTable = $serviceManager->get('Game\Model\PlayerTable');
		}
		return $this->playerTable;
	}
}

?>