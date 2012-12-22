<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Application\Form\AuthForm;
use Application\Form\InputFilter\AuthFormInputFilter;

class AuthController extends AbstractActionController {
	// Create and display login form
    public function indexAction() {
    	// Instantiate the authentication service
    	$auth = new AuthenticationService();
    	 
    	if($auth->hasIdentity()) {
    		return $this->redirect()->toRoute('game');
    	}
    	
        return array(
        	'form' => new AuthForm()
        );
    }
    
    // Authenticate the user based on the submited form values
    public function authAction() {
    	// Instantiate the authentication service
    	$auth = new AuthenticationService();
    	
    	if($auth->hasIdentity()) {
    		return $this->redirect()->toRoute('game');
    	}
    	
    	// Validate the form data
    	$form = new AuthForm();
    	$form->setInputFilter(new AuthFormInputFilter());
    	$form->setData($this->getRequest()->getPost());
    	
    	if(!$form->isValid()) {
    		// Re-render login form with error messages
    		$view = new ViewModel(array('form' => $form));
    		$view->setTemplate('application/auth/index');
    	} else {
	    	// Get the database adapter from the service manager
	    	$databaseAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
	    	
	    	// Set up the authentication adapter
	    	$authAdapter = new DbTableAuthAdapter($databaseAdapter);
	    	$authAdapter->setTableName('player')
	    				->setIdentityColumn('username')
	    				->setCredentialColumn('password');
	    	
	    	// Get username and password from login form
	    	$formData = $form->getData();
	    	$username = $formData['username'];
	    	$password = $formData['password'];
	    	
	    	// Set the input credential values
	    	$authAdapter->setIdentity($username)
	    				->setCredential($password);
	    	
	    	// Attempt authentication, saving the result
	    	$result = $auth->authenticate($authAdapter);
	    	
	    	if (!$result->isValid()) {
	    		// Authentication failed; re-render the login form with error message(s)
	    		$form->setMessages($result->getMessages());
	    		$view = new ViewModel(array(
	    				'errorMessages' => $result->getMessages(),
	    				'form' => $form
	    		));
	    		$view->setTemplate('application/auth/index');
	    	} else {
	    		// Store every column except password in the Auth SESSION storage
	    		$storage = $auth->getStorage();
	    		$storage->write($authAdapter->getResultRowObject(null, 'password'));
	    		
	    		// Redirect to the map page
	    		return $this->redirect()->toRoute('game');
	    	}
    	}
    	
    	return $view;
    }
    
    public function logoutAction() {
    	$auth = new AuthenticationService();
    	
    	if(! $auth->hasIdentity()) {
    		return $this->redirect()->toRoute('application', array(
    			'controller' => 'auth'
    		));
    	}
    	
    	$auth->clearIdentity();
    }
}

?>