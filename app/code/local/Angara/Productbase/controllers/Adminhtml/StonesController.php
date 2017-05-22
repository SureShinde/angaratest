<?php
class Angara_Productbase_Adminhtml_StonesController extends Mage_Adminhtml_Controller_Action
{
	
    protected function _initAction() {
        return $this->loadLayout()->_setActiveMenu('productbase/stones');
    }

    public function indexAction() { 
		$this->_initAction();
		
		$this->_addContent($this->getLayout()->createBlock('productbase/adminhtml_stones'));
		$this->renderLayout();
	}
	
	
	/**
     * Product grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('productbase/adminhtml_stones'));
        $this->renderLayout();
    }
}
