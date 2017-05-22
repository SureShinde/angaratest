<?php

ini_set("memory_limit","1024M");
class Webflaresolutions_Producttags_Model_Convert_Parser_Exporttags extends Mage_Catalog_Model_Convert_Parser_Product
{
   
    public function unparse()
    {
		$tagRelationModel = Mage::getResourceModel('tag/customer_collection');;
		
		$counter = array();
		$data = array();
		foreach($tagRelationModel as $tag_data_final )
		{
			$tag_data_first = array();
			$tag_data_first['email'] = $tag_data_final->getData('email');
			$tag_data_first['website_id'] = $tag_data_final->getData('website_id');
			$tag_data_first['tag_id'] = $tag_data_final->getData('tag_id');
			$tag_data_first['status'] = $tag_data_final->getData('status');
			$tag_data_first['name'] = $tag_data_final->getData('name');
			$tag_data_first['store_id'] = $tag_data_final->getData('store_id');		 // 
			array_push($data,$tag_data_first);
		}	

		$dubcounter = array_values(array_map("unserialize", array_unique(array_map("serialize", $data))));
 
		for($e=0; $e<count($dubcounter); $e++)
		{
	
		
        $collection = Mage::getResourceModel('tag/product_collection')->addTagFilter($dubcounter[$e]['tag_id']);		
		
	    $product = Mage::getModel('catalog/product')->load($dubcounter[$e]['entity_id']);
		$sku = $product->getSku();
		$customer_email = $dubcounter[$e]['email'];
		$customer = Mage::getModel("customer/customer");
		$customer->setWebsiteId($dubcounter[$e]['website_id']);
		$customer->loadByEmail($customer_email); 
		$customer_id = $customer->getId();
		
			foreach($collection as $collection_data )
			{
				$row = array(
					"sku"=>$collection_data->getData('sku'),
					"product_tags"=>$dubcounter[$e]['name'],
					"status"=>$dubcounter[$e]['status'],
					"store_id"=>$dubcounter[$e]['store_id'],
					"customer_id"=>$customer_id,
						);
				
				$batchExport = $this->getBatchExportModel()
					->setId(null)
					->setBatchId($this->getBatchModel()->getId())
					->setBatchData($row)
					->setStatus(1)
					->save();
			}
		}
	}
}

	
	
		

