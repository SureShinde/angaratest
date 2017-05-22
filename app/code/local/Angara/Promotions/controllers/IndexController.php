<?php
class Angara_Promotions_IndexController extends Mage_Core_Controller_Front_Action{
	
	protected function _construct(){
        
    }
	
	public function indexAction(){
		$this->loadLayout();    
  		$this->renderLayout();
	}
	
	public function ajaxAction(){
		$this->loadLayout();    
  		$this->renderLayout();
	}
}