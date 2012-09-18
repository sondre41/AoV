<?php

namespace Map\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
	protected $mapTable;
	
    public function indexAction()
    {
        return new ViewModel(array(
        	'mapSquares' => $this->getMapTable()->fetchAll()
        ));
    }
    
    private function getMapTable()
    {
    	if (!$this->mapTable) {
    		$serviceManager = $this->getServiceLocator();
    		$this->mapTable = $serviceManager->get('Map\Models\MapTable');
    	}
    	return $this->mapTable;
    }
}

?>