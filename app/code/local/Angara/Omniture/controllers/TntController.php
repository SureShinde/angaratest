<?php
class Angara_Omniture_TntController extends Mage_Core_Controller_Front_Action
{
    public function indexAction(){
    }
	
	public function setProductThemeAction(){
		$productTheme  = $this->getRequest()->getParam('theme');
		Mage::getSingleton('customer/session')->setProductTheme($productTheme);
    }
}
