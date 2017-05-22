<?php
class AngaraCustom_Function_Model_Observer
{
	public function runtCatalogPriceRangeIndexing($observer){
		$process = Mage::getSingleton('index/indexer')->getProcessByCode('tindexer_indexer');
		$process->reindexAll();
	}
	
	
	/*
		Saving the vendor_lead_time in sales_flat_quote_item table
	*/
	public function salesQuoteItemSetVendorLeadTime($observer){
		$quoteItem 	= 	$observer->getQuoteItem();
        $product 	= 	$observer->getProduct();
		Mage::log('Sku '.$product->getSku(), null, 'debug.log', true);
		$childProductSku	=	Mage::helper('function')->getRealChildSku($product->getSku());			//	This only gives child product sku
		//$product			=	$product->load($product->getId());
		$product			=	Mage::getModel('catalog/product')->loadByAttribute('sku', $childProductSku);
		if($product->getVendorLeadTime()){
			Mage::log('Vendor Lead Time for '.$product->getSku().' '.$product->getId().' is '.$product->getVendorLeadTime() , null, 'debug.log', true);
        	$quoteItem->setVendorLeadTime($product->getVendorLeadTime());
		}
	}
	
	
}