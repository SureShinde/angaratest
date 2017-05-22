<?php
class Mage_Digger_Model_Api extends Mage_Digger_Model_Resource
{
    public function items()
    {
		ini_set ( 'default_socket_time', 5000 );
		ini_set ( 'max_execution_time', 5000 );
		
		$categoryPosition = $this->_getRootCategories(2);
		
		$read = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_read' );
		$keys = $read->fetchAll ( "select distinct(cpei.entity_id) from catalog_product_entity_int as cpei
								where cpei.attribute_id = 91 and cpei.value = 4" );
		
		$result = array ();
		
		foreach ( $keys as $key ) {
			$product = $this->_getProduct ( ( int ) $key ['entity_id'], $store, $identifierType );
			if ($product->getData ( 'visibility' ) == '4') {				
				$result [$product->getId ()] = array (	
					'product_id' => $product->getId (), 
					'categories' => $product->getCategoryIds (),
					'top_category_position' => $categoryPosition[$product->getId()]['position'],
					'top_category_weight' => $categoryPosition[$product->getId()]['product_ratio']
				);
				
				$result [$product->getId ()] ['visibility'] = $product->getData ( 'visibility' );
				foreach ( $product->getTypeInstance ( true )->getEditableAttributes ( $product ) as $attribute ) {
					if ($this->_isAllowedAttribute ( $attribute, $attributes ) && $attribute->getIsSearchable () == 1) {
						$result [$product->getId ()] [$attribute->getAttributeCode ()] = ($attribute->getFrontendInput () == 'select') ? $product->getAttributeText ( $attribute->getAttributeCode () ) : (($attribute->getFrontendInput () == 'multiselect') ? implode ( ' ', $product->getAttributeText ( $attribute->getAttributeCode () ) ) : $product->getData ( $attribute->getAttributeCode () ));
					}
				}
			
			}
		}
		if(count($result)==0){
			return 'no result found';
		}
		return $result;
    }
	
	public function update($product_id,$keyword,$relevancy,$normalizer)
	{
		try{
			$write = Mage::getSingleton("core/resource")->getConnection("core_write");
			$query = "insert into product_keyword_relevancy (product_id, keyword, relevancy, normalizer) values (:product_id, :keyword, :relevancy, :normalizer)
					ON DUPLICATE KEY UPDATE 
  					product_id=:product_id,keyword=:keyword,relevancy=:relevancy, normalizer=:normalizer";
			$binds = array(
				'product_id'      => $product_id,
				'keyword'     => $keyword,
				'relevancy'   => $relevancy,
				'normalizer'      => $normalizer
			);
			
			$write->query($query, $binds);
		} catch ( Mage_Core_Exception $e ) {
			$this->_fault ( 'data_invalid', $e->getMessage () );
			return false;
		}
		return true;
	}
	
	public function truncate()
	{
		$write = Mage::getSingleton("core/resource")->getConnection("core_write");
		$query = 'TRUNCATE product_keyword_relevancy';
		$write->query($query, $binds);
		return 'table is product_keyword_relevancy empty';
	}
	
	private function _getRootCategories($categoryId, $store = NULL){
		$read = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_read' );
		$categoryProductPosition = $read->fetchAll ( "select CCP.category_id as category, CCP.product_id, CCP.position from catalog_category_product as CCP
										left join catalog_product_entity as CPE on CPE.entity_id = CCP.product_id
										left join catalog_product_entity_int as CPEI on CPEI.entity_id = CCP.product_id
										where CPEI.attribute_id = 91 and CPEI.value != 1" );
										
		$category = Mage::getModel('catalog/category')
            ->setStoreId($this->_getStoreId($store))
            ->load($categoryId);
		if (!$category->getId()) {
            $this->_fault('not_exists');
        }
		
		$read = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_read' );
		$rootCategories = $read->fetchAll ( "select CCP.category_id, count(CCP.product_id) as count from catalog_category_product as CCP
										where CCP.category_id in(".$category->getChildren().")
										group by CCP.category_id");
		$sum = 0;
		foreach($rootCategories as $key=>$element){
			$newarray[$element['category_id']] = $element['count'];
			$sum += (int) $element['count']; 
		}
		$temparray = explode(',',$category->getChildren());
		$finalarray = array();
		foreach($categoryProductPosition as $element){
			if(gettype(array_search($element['category'],$temparray))=="integer"){
				if(isset($finalarray['category']) && (int)$finalarray['category']<(int)$element['category']){
					$finalarray[$element['product_id']] = $element;
				}
				else $finalarray[$element['product_id']] = $element;
			}
		}
		foreach($finalarray as $key=>$element){
			$finalarray[$key]['product_ratio'] = (int)$newarray[$element['category']]/$sum;
		}
		return $finalarray;
	}
}
?>