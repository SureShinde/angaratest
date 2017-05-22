<?php
class hp_hprcv_sliderController extends Mage_Core_Controller_Front_Action
{
	
	public function getslidesAction(){
		$_category = $this->getRequest()->getParam('title');
		Mage::getModel('hprcv/hprcv')->getSlides($_category);
	}
	
	public function getmainbannerslidesAction(){
		$_category = $this->getRequest()->getParam('category');
		Mage::getModel('hprcv/hprcv')->getMainBannerSlides($_category);
	}
	
	public function validateloginemailAction()
	{	
		$email = $this->getRequest()->getParam('email');
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$table = $resource->getTableName('customer_entity');
		$query = 'SELECT email FROM ' . $table . ' WHERE email = "'.$email.'";';
		$result = $readConnection->fetchAll($query);
		$numRows = count($result);
		echo $numRows;
	}
}
?>