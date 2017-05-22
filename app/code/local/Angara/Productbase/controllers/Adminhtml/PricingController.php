<?php
class Angara_Productbase_Adminhtml_PricingController extends Mage_Adminhtml_Controller_Action
{
	
    protected function _initAction() {
        return $this->loadLayout()->_setActiveMenu('productbase/pricing');
    }

    public function indexAction() { 
		$this->_initAction();
		
		$this->_addContent($this->getLayout()->createBlock('productbase/adminhtml_pricing'));
		$this->renderLayout();
	}
	
	public function calculateAction() {
		
		$data = $this->getRequest()->getPost();
		Mage::getSingleton('core/session')->setPricingData($data);
		
		// #todo get dynamic sku
		$data['sku'] = 'TMP';
		
		// #validateParams
		try{
			$dummyProduct = Mage::getModel('productbase/dummyProduct');
			$price = $dummyProduct->calculatePrice($data);
		}
		catch(Exception $e){
			var_dump($e);
			//echo $e->getError();
		}
		
		echo $price;
		//echo " ";
	}
	
	public function saveAction(){
		$data = $this->getRequest()->getPost();
		Mage::getSingleton('core/session')->setPricingData($data);
		
		// #todo get dynamic sku
		//$data['sku'] = 'TMP'.rand(1000,9999);
		// #validateParams
		$dummyProduct = Mage::getModel('productbase/dummyProduct');
		$product = $dummyProduct->save($data);
		if(!$product){
			$this->_getSession()->addError("There is a problem saving product. Please report to technical team.");
		} else {
			Mage::getSingleton('core/session')->setCustomProduct($product);
			Mage::getSingleton('core/session')->setCustomProductParams($data);
			$this->_getSession()->addSuccess("Product successfully created.");
			$this->_redirect('*/*/view');
		}
		
	}
	
	public function viewAction(){
		$this->_initAction();
		//var_dump($this->getLayout()->createBlock('productbase/adminhtml_customproduct'));
		$block = $this->getLayout()->createBlock('productbase/adminhtml_customproduct');
		$block->setProduct(Mage::getSingleton('core/session')->getCustomProduct());
		$block->setCartParams(Mage::getSingleton('core/session')->getCustomProductParams());
		//$block->getCartLink();
		$this->_addContent($block);
		$this->renderLayout();
	}
	
	public function getStoneSizesHashAction(){
		$stoneid = $this->getRequest()->getParam('stoneid');
		$shapeid = $this->getRequest()->getParam('shapeid');
		$weights = Mage::getModel('productbase/stone_weight')->getCollection()->addFieldToFilter('stone_id',$stoneid)->addFieldToFilter('shape_id',$shapeid)->getItems();
		$sizeOptions = '<option value="None">Please Select</option>';
		foreach($weights as $weight){
			$sizeOptions .= '<option value="'.$weight->getStoneSize().'">'.$weight->getStoneSize().'</option>';
		};
		echo $sizeOptions;
	}
	
	public function getStoneSettingsHashAction(){
		$size = $this->getRequest()->getParam('size');
		$settings = Mage::getModel('productbase/settingtype')->getCollection()->addFieldToFilter('stone_size',$size)->getItems();
		$settingOptions = '<option value="None">Please Select</option>';
		foreach($settings as $setting){
			$settingOptions .= '<option value="'.$setting->getSettingName().'">'.$setting->getSettingName().'</option>';
		};
		echo $settingOptions;
	}
	
	
	private function _getProductPrice($data){
		
		
		
	}
}
