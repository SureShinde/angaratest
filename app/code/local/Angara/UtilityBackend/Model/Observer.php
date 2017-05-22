<?php
class Angara_UtilityBackend_Model_Observer
{
	public function changeProductName(Varien_Event_Observer $observer)
	{
		$item = $observer->getQuoteItem();
		if($item && $item->getId()){
			$simpleProduct = Mage::getModel('catalog/product')->loadByAttribute('sku', $item->getSku());
			if($simpleProduct && $simpleProduct->getId()) {
				$item->setName($simpleProduct->getShortDescription());
			}
		}	
        return $item;
	}
	/**
	 * Forcefully enable PayPal Express
	 * @author Asheesh
	 */
	public function forceEnablePayPalExpress(Varien_Event_Observer $observer) {
		try {
			$groups = Mage::app()->getRequest()->getPost('groups');
			$website = $observer->getWebsite();
			$store   = $observer->getStore();
			$scope = 'default';
			$scopeId = 0;
			$config = new Mage_Core_Model_Config();
			if(strlen($store) > 0) {
				$sc = Mage::getModel( "core/store" )->load($store);
				if($sc && $sc->getId()) {
					$scope = 'stores';
					$scopeId = $sc->getId();
				}
			} elseif(strlen($website) > 0) {
				$sc = Mage::getModel( "core/website" )->load($website);
				if($sc && $sc->getId()) {
					$scope = 'websites';
					$scopeId = $sc->getId();
				}
			} else {
				$scope = 'default';
				$scopeId = 0;
			}
			$config->saveConfig('payment/paypal_express/active',1,$scope,$scopeId);
			$config->saveConfig('payment/paypaluk_express_bml/active',0,$scope,$scopeId);
			$config->saveConfig('payment/paypal_express_bml/active',0,$scope,$scopeId);
			if(isset($groups['incontext']['fields']['merchantid']['value'])) {
				$config->saveConfig('payment/incontext/merchantid',$groups['incontext']['fields']['merchantid']['value'],$scope,$scopeId);
			}
			Mage::app()->reinitStores();
		} catch(Exception $e) {
			Mage::logException($e);
		}
	}
}