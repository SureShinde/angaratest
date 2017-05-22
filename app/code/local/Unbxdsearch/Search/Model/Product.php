<?php 

class Unbxdsearch_Search_Model_Product extends Mage_Catalog_Model_Resource_Product_Collection{
	
	public function getNumFound($numFound){
		$this->_totalRecords=$numFound;
	}
} 

?>