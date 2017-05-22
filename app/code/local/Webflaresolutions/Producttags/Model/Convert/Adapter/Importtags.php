<?php

class Webflaresolutions_Producttags_Model_Convert_Adapter_Importtags
extends Mage_Catalog_Model_Convert_Adapter_Product
{
    public function load() {
  
    }

    public function save() {
     
    }

    public function saveRow(array $importData) {
    $product = $this->getProductModel ();
    $product->setData ( array () );
    if ($stockItem = $product->getStockItem ()) {
        $stockItem->setData ( array () );
    }
    $product = Mage::getModel('catalog/product');
    $productId='';
    $productId = $product->getIdBySku($importData['sku']); 
	$tagNames=array();
    $tagNames = explode(',', $importData['product_tags']);
    $storeId = explode(',', $importData['store_id']);
    $customerId = explode(',', $importData['customer_id']);
	$status = $importData['status'];
	
	
    if (empty ( $importData ['sku'] )) {
        $message = Mage::helper ( 'catalog' )->__ ( 'Skip import row, required field "%s" not defined', 'sku' );
        Mage::throwException ( $message );
    }       
    if ( !$productId ) {
        $message = Mage::helper ( 'catalog' )->__ ( 'Skip import row, required field "%s" not Valid Sku', $importData['sku'] );
        Mage::throwException ( $message );
    }
	for($r=0; $r<=count($tagNames); $r++)
	for($s=0; $s<=count($storeId); $s++)
	for($t=0; $t<=count($customerId); $t++)
	{
    if(strlen($tagNames[$r]) && $productId) {
        $session = Mage::getSingleton('catalog/session');
        $product = Mage::getModel('catalog/product')
            ->load($productId);
            $productId =$product->getId();
        if(!$product->getId()){
           $message = Mage::helper ( 'catalog' )->__ ( 'Skip import row, required field "%s" not valid', 'sku' );
            Mage::throwException ( $message );
        } else {
            try {
              $tagModel='';                 
              $counter = new Varien_Object(array(
                        "new" => 0,
                        "exist" => array(),
                        "success" => array(),
                        "recurrence" => array())
                        );                  
               $tagModel = Mage::getModel('tag/tag');
               $tagRelationModel = Mage::getModel('tag/tag_relation');
               $tagNamesArr = $this->_cleanTags($this->_extractTags($tagNames[$r]));
               foreach ($tagNamesArr as $tagName) {
                    $tagModel->unsetData()
                            ->loadByName($tagName)
                            ->setName($tagName)
                            ->setFirstCustomerId($customerId[$t])
                            ->setFirstStoreId($storeId[$s])
                            ->setStatus($status)
                            ->save();

                    $tagRelationModel->unsetData()
                        ->setStoreId($storeId[$s])
                        ->setProductId($productId)
                        ->setCustomerId($customerId[$t])
                        ->setActive(Mage_Tag_Model_Tag_Relation::STATUS_ACTIVE)
                        ->setCreatedAt( $tagRelationModel->getResource()->formatDate(time()) );

                    if (!$tagModel->getId()) {  
                        $tagModel->setName($tagName)
                            ->setFirstCustomerId($customerId[$t])
                            ->setFirstStoreId($storeId[$S])
                            ->setStatus($tagModel->getPendingStatus())
                            ->save();
                        $relationStatus = $tagModel->saveRelation($productId, $customerId[$t], $storeId[$s]);
                        $counter[$relationStatus][] = $tagName; 
                        $tagRelationModel->setTagId($tagModel->getId())->save();
                        $counter->setNew($counter->getNew() + 1);
                    }
                    else { 
                        $tagStatus = $tagModel->getStatus();
                        $tagRelationModel->setTagId($tagModel->getId());
                        $relationStatus = $tagModel->saveRelation($productId, $customerId[$t],'');
                        $counter[$relationStatus][] = $tagName; 
                        switch($tagStatus) {
                            case $tagModel->getApprovedStatus(): 
                                if($this->_checkLinkBetweenTagProduct($tagRelationModel)) { 
                                    $relation = $this->_getLinkBetweenTagCustomerProduct($tagRelationModel, $tagModel);
                                    if ($relation->getId()) {
                                        if (!$relation->getActive()) {
                                            $tagRelationModel
                                                ->setId($relation->getId())
                                                ->save();
                                        }
                                    } 
                                    else { 
                                        $tagRelationModel->save();
                                    }                                       
                                    $counter->setExist(array_merge($counter->getExist(), array($tagName)));
                                } 
                                else { 
                                    $tagRelationModel->save(); 
                                    $counter->setSuccess(array_merge($counter->getSuccess(), array($tagName)));
                                }
                                break;
                            case $tagModel->getPendingStatus(): 
                                $relation = $this->_getLinkBetweenTagCustomerProduct($tagRelationModel, $tagModel);
                                if ($relation->getId()) {
                                    if (!$relation->getActive()) {
                                        $tagRelationModel
                                            ->setId($relation->getId())
                                            ->save();
                                    }
                                } 
                                else {
                                    $tagRelationModel->save();
                                }
                                $counter->setNew($counter->getNew() + 1);
                                break;
                            case $tagModel->getDisabledStatus():
                                if($this->_checkLinkBetweenTagCustomerProduct($tagRelationModel, $tagModel)) {
                                    $counter->setRecurrence(array_merge($counter->getRecurrence(), array($tagName)));
                                } 
                                else {
                                    $tagModel->setStatus($tagModel->getPendingStatus())->save();
                                    $tagRelationModel->save();
                                    $counter->setNew($counter->getNew() + 1);
                                }
                            break;
                        }
                    }
                }
            } catch (Exception $e) {
                Mage::logException($e);
                $message='Unable to save tag(s).';
                Mage :: throwException( $e.$message );
            }
        }
    } 
}	
    return true;
}

   protected function _getLinkBetweenTagCustomerProduct($tagRelationModel, $tagModel){
        return Mage::getModel('tag/tag_relation')->loadByTagCustomer(
            $tagRelationModel->getProductId(),
            $tagModel->getId(),
            $tagRelationModel->getCustomerId(),
            $tagRelationModel->getStoreId()
          );
    }

     protected function _checkLinkBetweenTagCustomerProduct($tagRelationModel, $tagModel){
        return (count($this->_getLinkBetweenTagCustomerProduct($tagRelationModel, $tagModel)
            ->getProductIds()) > 0);
    }   
    protected function _checkLinkBetweenTagProduct($tagRelationModel){      
        $customerId = $tagRelationModel->getCustomerId();
        $tagRelationModel->setCustomerId(null);
        $res = in_array($tagRelationModel->getProductId(), $tagRelationModel->getProductIds());
        $tagRelationModel->setCustomerId($customerId); 
        return $res;
    }
    protected function _cleanTags(array $tagNamesArr){
        foreach( $tagNamesArr as $key => $tagName ){
            $tagNamesArr[$key] = trim($tagNamesArr[$key], '\'');
            $tagNamesArr[$key] = trim($tagNamesArr[$key]);
            if( $tagNamesArr[$key] == '' ) {
                unset($tagNamesArr[$key]);
            }
        }
        return $tagNamesArr;
    }
    protected function _extractTags($tagNamesInString){
        //return explode("\n", preg_replace("/(\'(.*?)\')|(\s+)/i", "$1\n", $tagNamesInString));
		return explode("\n", preg_replace("/(\'(.*?)\')/i", "$1\n", $tagNamesInString));			//	S:VA	space issue resolved
    }   
    protected function userCSVDataAsArray($data) {
        return explode ( ',', str_replace ( " ", "", $data ) );
    }
    protected function skusToIds($userData, $product) {
        $productIds = array ();
        foreach ( $this->userCSVDataAsArray ( $userData ) as $oneSku ) {
            if (($a_sku = ( int ) $product->getIdBySku ( $oneSku )) > 0) {
                parse_str ( "position=", $productIds [$a_sku] );
            }
        }
        return $productIds;
    }

    protected $_categoryCache = array ();
    protected function _addCategories($categories, $store) {
        $rootId = $store->getRootCategoryId ();
        if (! $rootId) {
            return array ();
        }
        $rootPath = '1/' . $rootId;
        if (empty ( $this->_categoryCache [$store->getId ()] )) {
            $collection = Mage::getModel ( 'catalog/category' )->getCollection ()->setStore ( $store )->addAttributeToSelect ( 'name' );
            $collection->getSelect ()->where ( "path like '" . $rootPath . "/%'" );
            foreach ( $collection as $cat ) {
                $pathArr = explode ( '/', $cat->getPath () );
                $namePath = '';
                for($i = 2, $l = sizeof ( $pathArr ); $i < $l; $i ++) {
                    $name = $collection->getItemById ( $pathArr [$i] )->getName ();
                    $namePath .= (empty ( $namePath ) ? '' : '/') . trim ( $name );
                }
                $cat->setNamePath ( $namePath );
            }               
            $cache = array ();
            foreach ( $collection as $cat ) {
                $cache [strtolower ( $cat->getNamePath () )] = $cat;
                $cat->unsNamePath ();
            }
            $this->_categoryCache [$store->getId ()] = $cache;
        }
        $cache = & $this->_categoryCache [$store->getId ()];            
        $catIds = array ();
        foreach ( explode ( ',', $categories ) as $categoryPathStr ) {
            $categoryPathStr = preg_replace ( '#s*/s*#', '/', trim ( $categoryPathStr ) );
            if (! empty ( $cache [$categoryPathStr] )) {
                $catIds [] = $cache [$categoryPathStr]->getId ();
                continue;
            }
            $path = $rootPath;
            $namePath = '';
            foreach ( explode ( '/', $categoryPathStr ) as $catName ) {
                $namePath .= (empty ( $namePath ) ? '' : '/') . strtolower ( $catName );
                if (empty ( $cache [$namePath] )) {
                    $cat = Mage::getModel ( 'catalog/category' )->setStoreId ( $store->getId () )->setPath ( $path )->setName ( $catName )->// comment out the following line if new categories should stay inactive
                    setIsActive ( 1 )->save ();
                    $cache [$namePath] = $cat;
                }
                $catId = $cache [$namePath]->getId ();
                $path .= '/' . $catId;
            }
            if ($catId) {
                $catIds [] = $catId;
            }
        }
        return join ( ',', $catIds );
    }
}   	  		

	
