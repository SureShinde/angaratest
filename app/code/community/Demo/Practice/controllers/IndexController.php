<?php
class Demo_Practice_IndexController extends Mage_Core_Controller_Front_Action
{
	public function cronStatusAction(){
		Mage::getModel('practice/Observer')->cronStatus();
	}
}