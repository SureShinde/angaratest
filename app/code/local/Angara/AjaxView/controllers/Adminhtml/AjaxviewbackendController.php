<?php
class Angara_AjaxView_Adminhtml_AjaxviewbackendController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Ajax View Backend Page Title"));
	   $this->renderLayout();
    }
}