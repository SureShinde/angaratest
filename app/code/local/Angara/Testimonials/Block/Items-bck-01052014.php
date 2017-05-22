<?php

class Angara_Testimonials_Block_Items extends Mage_Core_Block_Template
{
	
	protected $_itemsCollection;
	
	public function getTestimonials()
    {	
		$itemscount = Mage::getStoreConfig('testimonials/general/itemscount');
		$itemscount = $itemscount?$itemscount:5;
		$this->_itemsCollection = Mage::getModel('testimonials/reviewdetail')->getCollection()->setPageSize($itemscount)->setCurPage(0)->getData();//->setPageSize($itemscount)->setCurPage(0)->setOrder('time','DESC');
		//->setPageSize($itemscount)->setCurPage(0)->setOrder('time','DESC');
		return $this->_itemsCollection;
    }
	public function getReviewsCollection()
    {
        //if (null === $this->_itemsCollection) {
			$itemscount = Mage::getStoreConfig('testimonials/general/itemscount');
			$page['page']=1;
			$page=$this->getRequest()->getParams();
			$itemscount = $itemscount?$itemscount:5;
            $this->_itemsCollection = Mage::getModel('review/review')->getCollection()->addFilter('status_id',1)
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                /*->addEntityFilter('product', $this->getProduct()->getId())*/
                ->setDateOrder()->setPageSize($itemscount)->setCurPage($page['page'])->addRateVotes();
        //}
        return $this->_itemsCollection;
    }
	public function getItemscount(){
		$itemscount = Mage::getStoreConfig('testimonials/general/itemscount');
		$itemscount = $itemscount?$itemscount:5;
		return $itemscount;
	}
	
	public function getReviewscount(){
		$Collection = Mage::getModel('review/review')->getCollection()->addFilter('status_id',1)
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED);
		return $Collection;		
	}
	
	public function getReviews()
    {
        //if (null === $this->_itemsCollection) {
			$reviewscount = Mage::getStoreConfig('testimonials/general/reviewscount');
			$page=$this->getRequest()->getParams();
			$reviewscount = $reviewscount?$reviewscount:5;
            $this->_itemsCollection = Mage::getModel('review/review')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                /*->addEntityFilter('product', $this->getProduct()->getId())*/
                ->setDateOrder()->setPageSize($reviewscount)->setCurPage(0)->addRateVotes();
        //}
		//echo $this->_itemsCollection->getSelect(); exit;
        return $this->_itemsCollection;
    }
	
	public function getCatalogReviews()
    {
		$category = Mage::registry('current_category'); 
		$cur_category = Mage::getModel('catalog/category')->load($category->getId());    
		$products = Mage::getResourceModel('catalog/product_collection')
			->addCategoryFilter($cur_category)
			->addAttributeToFilter('visibility','4')
			;
		$prod_arr = array();
		foreach ( $products as $productModel )
		{
			$prod_arr[] = $productModel->getId();
		}
		//echo '<pre>';print_r($prod_arr);exit; 
			$reviewscount = Mage::getStoreConfig('testimonials/general/reviewscount');
			$page=$this->getRequest()->getParams();
			$reviewscount = $reviewscount?$reviewscount:5;
            $this->_itemsCollection = Mage::getModel('review/review')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
				->addFieldToFilter('entity_pk_value',array('in'=>$prod_arr))				
			    ->setDateOrder()->setPageSize($reviewscount)->setCurPage(0)->addRateVotes();
        
		//echo $this->_itemsCollection->getSelect()->__toString(); exit;
        return $this->_itemsCollection;
    }
	
	
	//	Function added by Vaseem to get product reviews based on category id
	public function getReviewsForCategory($categoryId,$reviewscount=1)
    {
		//echo '<br>reviewscount->'.$reviewscount;
		if(!$categoryId) {return false;}
		$cur_category = Mage::getModel('catalog/category')->load($categoryId);    
		//	Get available products for this category
		if($categoryId=='406' || $categoryId=='407' || $categoryId=='408'){
			$products = Mage::getResourceModel('catalog/product_collection')
							->addCategoryFilter($cur_category)
							->addAttributeToFilter('status', 1)
							//->addAttributeToFilter('visibility','4')
							->addAttributeToSort('entity_id', 'DESC')
							//->load(1)
							;
		}else{
			$products = Mage::getResourceModel('catalog/product_collection')
							->addCategoryFilter($cur_category)
							->addAttributeToFilter('status', 1)
							->addAttributeToFilter('visibility','4')
							->addAttributeToSort('entity_id', 'DESC')
							//->load(1)
							;
		}
		$prod_arr = array();
		foreach ( $products as $productModel )
		{
			$prod_arr[] = $productModel->getId();
		}
		//echo '<pre>';print_r($prod_arr);exit; 
		//$reviewscount = Mage::getStoreConfig('testimonials/general/reviewscount');
		$page=$this->getRequest()->getParams();
		//$reviewscount = $reviewscount?$reviewscount:1;
		$this->_itemsCollection = Mage::getModel('review/review')->getCollection()
				->addStoreFilter(Mage::app()->getStore()->getId())
				->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
				->addFieldToFilter('entity_pk_value',array('in'=>$prod_arr))				
				->setDateOrder()->setPageSize($reviewscount)->setCurPage(0)->addRateVotes();
        
		//echo $this->_itemsCollection->getSelect()->__toString(); exit;
        return $this->_itemsCollection;
    }
	
	public function getCategoryId()
    {
		return Mage::registry('review_category_id');
	}
	
	public function getReviewCategoryIds(){
		$categoryIds	=	 Mage::getStoreConfig('testimonials/general/reviews_category_ids');
		if($categoryIds==''){
			$categoryIds	=	 '324,323';
		}
		return $categoryIds;
	}
	
	public function getOtherCategoryIds(){
		$categoryIds	=	 Mage::getStoreConfig('testimonials/general/other_reviews_category_ids');
		if($categoryIds==''){
			$categoryIds	=	 '324,323';
		}
		return $categoryIds;
	}
	
	public function getLeftCategoryIds($parentCatId){
		//	Add a Parent Category Id here
		$parentCatArray		=	array('59','58','56','60','4','211','312','314','313','310','374','311','61','371','77','206','99','83','82','85','249','257','253','3','349','325','406');				
		//	Create an array of Child Category Ids here
		$childCatArray[59] 	= 	array('33','48','40','78');
		$childCatArray[58] 	= 	array('34','49','41','79');
		$childCatArray[56] 	= 	array('35','50','42','80');
		$childCatArray[60] 	= 	array('36','51','43','81');
		$childCatArray[4] 	= 	array('70','71','72','82');
		$childCatArray[211] = 	array('211');
		$childCatArray[312] = 	array('300','317','306');
		$childCatArray[314] = 	array('302','319','308');
		$childCatArray[313] = 	array('301','318','307');
		$childCatArray[310] = 	array('298','315','304');
		$childCatArray[374] = 	array('373','376','375','365');
		$childCatArray[311] = 	array('299','316','305');
		$childCatArray[61] 	= 	array('37','52','44');
		$childCatArray[371] = 	array('368','369','370');
		$childCatArray[77] 	= 	array('303','320','309');
		$childCatArray[206] = 	array('264','266','267');
		$childCatArray[99] 	= 	array('271','297','296');
		$childCatArray[83] 	= 	array('78','79','80','81');
		$childCatArray[82] 	= 	array('366','367','82');
		$childCatArray[85] 	= 	array('399','400','328');
		$childCatArray[249] = 	array('250','251','252');
		$childCatArray[257] = 	array('258','259','260');
		$childCatArray[253] = 	array('254','255','256');
		$childCatArray[3] 	= 	array('64','62','63','332');
		$childCatArray[349] = 	array('351');
		$childCatArray[325] = 	array('327','329','333','336','352');
		$childCatArray[406] = 	array('407','408','409'); 
	
		if( in_array($parentCatId,$parentCatArray) ){
			//echo 'Return Child Category Ids.';
			return $childCatArray[$parentCatId];
		}else{
			//echo 'Return Parent Category Id.';
			//echo '<pre>'; print_r($childCatArray);
			foreach($childCatArray as $child => $value){
				//echo 'child->'.$child;
				//echo '<pre>'; print_r($value);
				//echo '<br>parentCatId->'.$parentCatId;
				//print_r($value);
				if( in_array($parentCatId,$value) ){
					$parentCatId =	$child;
					return $parentCatId;
				}/*else{
					echo 'Return Main Categories';
					 $backendParentCatIds	=	$this->getReviewCategoryIds();
					 //echo '<br>'.$backendParentCatIds;
					 $backendParentCatIdsArray	=	explode(',',$backendParentCatIds);
					 //echo '<pre>'; print_r($backendParentCatIdsArray);die;
					 return $backendParentCatIdsArray;
				}*/
			}
		}
	}
	
}