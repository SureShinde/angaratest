<?php
class Angara_Gemstonecolor_Model_Observer
{
	public function initProductSave(Varien_Event_Observer $observer)
	{
		$product = $observer->getEvent()->getProduct();
		$request = $observer->getEvent()->getRequest();
		$links = $request->getPost('links');
		
		if (isset($links['gemstonecolor']) && !$product->getGemstonecolorReadonly()) {
			$data = Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['gemstonecolor']);
			/* foreach($data as $productId=>$gemstonecolors) {
				if(isset($data[$productId]['position'])) {
					$data[$productId]['gemcolorimage'] = $data[$productId]['position'];
					unset($data[$productId]['position']);
				}
			} */
			$product->setGemstonecolorLinkData($data);
		}
		
        return $this;
	}
}