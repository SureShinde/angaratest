<?php
class Angara_Matchingband_IndexController extends Mage_Core_Controller_Front_Action{
	
	protected function _construct()
    {
        
    }
	
	public function indexAction()
    {
		$id = $this->getRequest()->getParam('id');
		if($id){
			$product = Mage::getModel('catalog/product')->load($id);
			if($product){
				Mage::register('product', $product);
			}
		}
		$this->loadLayout();
		$this->renderLayout();
    }
}