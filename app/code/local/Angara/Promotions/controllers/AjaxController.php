<?php
class Angara_Promotions_AjaxController extends Mage_Core_Controller_Front_Action
{
	protected function _construct(){
        
    }
	
	public function indexAction(){
		$this->loadLayout();    
  		$this->renderLayout();
	}
}