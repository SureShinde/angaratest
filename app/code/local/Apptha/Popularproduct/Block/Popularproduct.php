<?php
class  Apptha_Popularproduct_Block_Popularproduct extends Mage_Catalog_Block_Product_Abstract
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

     public function getPopularproduct()
     {
        if (!$this->hasData('popularproduct')) {
            $this->setData('popularproduct', Mage::registry('popularproduct'));
        }
        return $this->getData('popularproduct');

    }

    public function getProductCollection(){

        $strStoreId=Mage::app()->getStore()->getStoreId();
       $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
       $model = Mage::getResourceModel('reports/product_collection');

       $collection = $model->getCollection()
                             ->addAttributeToSelect('*')
                             ->setStoreId($storeId)
                            ->addStoreFilter($storeId)
                            ->addViewsCount();

       //$collection->setPageSize($limit);
        return $collection;


    }
}