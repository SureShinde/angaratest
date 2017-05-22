<?php
class AngaraCustom_Function_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		//echo 'test';
    }
	
	public function cronBlogAction()
    {
		Mage::getModel('angaracustom_function/function')->cron();
    }
	
	
}