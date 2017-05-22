<?php
class Angara_Promotions_Adminhtml_PromotionsbackendController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Angara Promotions"));
	   $this->renderLayout();
    }
}