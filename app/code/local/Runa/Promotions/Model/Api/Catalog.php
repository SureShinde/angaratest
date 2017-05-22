<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Runa_Promotions_Model_Api_Catalog extends Mage_Catalog_Model_Product_Api {

    public function __construct()
    {
        $_authKey = mage::getModel('runapromotions/config_settings')->getClientAuthKey();
        if (mage::app()->getRequest()->get('secret_key') != $_authKey)
        {
            throw new Zend_Exception("Invalid key: (Autorization failed)", 'FATAL');
        }
    }

    /**
     *  @return SimpleXMLElement
     */
    public function get_categories()
    {
        $_catigCollection = mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('*');
        $_catigs = array();
        foreach ($_catigCollection as $_catig)
        {
            $_catigData['code'] = $_catig->getId();
            $_catigData['name'] = $this->_wrapInCDATA($_catig->getName());
            $_parent_id = "";
            if( $_catig->getParentId()){
             $_parent_id =  $_catig->getParentId();
            }
            $_catigData['parent-code'] = $_parent_id;
            $_catigs [] = $_catigData;
        }

        $_result['category'] = $_catigs;
        $_xmlCreator = mage::getModel('runapromotions/api_response_processor');
        /* @var $_xmlCreator Runa_Promotions_Model_Api_Response_Processor */
        
        $_xmlCategory = $_xmlCreator->toXML($_result, "categories");
        $_xmlCategory = simplexml_load_string($_xmlCategory);

        return $_xmlCategory;
    }

    /**
     * Retrieve list of products with basic info (id, sku, type, set, name)
     *
     * @param integer $pageNum
     * @param integer $pageSize
     * @return array
     */
    public function fetch($pageNum, $pageSize)
    {

        $collection = Mage::getModel('catalog/product')->getCollection()
                        ->setStoreId($this->_getStoreId($store))
                        ->addAttributeToSelect('name')
                        ->setPageSize($pageSize)
                        ->setCurPage($pageNum);

        $preparedFilters = array();
        if (isset($filters->filter))
        {
            foreach ($filters->filter as $_filter)
            {
                $preparedFilters[$_filter->key] = $_filter->value;
            }
        }
        if (isset($filters->complex_filter))
        {
            foreach ($filters->complex_filter as $_filter)
            {
                $_value = $_filter->value;
                $preparedFilters[$_filter->key] = array(
                    $_value->key => $_value->value
                );
            }
        }

        if (!empty($preparedFilters))
        {
            try {
                foreach ($preparedFilters as $field => $value)
                {
                    if (isset($this->_filtersMap[$field]))
                    {
                        $field = $this->_filtersMap[$field];
                    }

                    $collection->addFieldToFilter($field, $value);
                }
            } catch (Mage_Core_Exception $e) {
                $this->_fault('filters_invalid', $e->getMessage());
            }
        }

        $result = array();

        $_attributeApi = mage::getModel('catalog/product_attribute_api');
        /* @var $_attributeApi Mage_Catalog_Model_Product_Attribute_Api */
        foreach ($collection as $product)
        {

            $result[] = array(
                'product_id' => $product->getId(),
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'set' => $product->getAttributeSetId(),
                'type' => $product->getTypeId(),
                'website_ids' => $product->getWebsiteIds(),
                    //'attributes'=>   $options
            );
        }

        return $result;
    }

    private function _wrapInCDATA($text)
    {
        return "<![CDATA[$text]]>";
    }

}
