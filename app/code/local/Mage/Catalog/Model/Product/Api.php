<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product api
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Api extends Mage_Catalog_Model_Api_Resource
{
    protected $_filtersMap = array(
        'product_id' => 'entity_id',
        'set'        => 'attribute_set_id',
        'type'       => 'type_id'
    );

    protected $_defaultProductAttributeList = array(
        'type_id',
        'category_ids',
        'website_ids',
        'name',
        'description',
        'short_description',
        'sku',
        'weight',
        'status',
        'url_key',
        'url_path',
        'visibility',
        'has_options',
        'gift_message_available',
        'price',
        'special_price',
        'special_from_date',
        'special_to_date',
        'tax_class_id',
        'tier_price',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'custom_design',
        'custom_layout_update',
        'options_container',
        'image_label',
        'small_image_label',
        'thumbnail_label',
        'created_at',
        'updated_at'
    );

    public function __construct()
    {
        $this->_storeIdSessionField = 'product_store_id';
        $this->_ignoredAttributeTypes[] = 'gallery';
        $this->_ignoredAttributeTypes[] = 'media_image';
    }

    /**
     * Retrieve list of products with basic info (id, sku, type, set, name)
     *
     * @param array $filters
     * @param string|int $store
     * @return array
     */
    public function items($filters = null, $store = null)
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addStoreFilter($this->_getStoreId($store))
            ->addAttributeToSelect('name');

        if (is_array($filters)) {
            try {
                foreach ($filters as $field => $value) {
                    if (isset($this->_filtersMap[$field])) {
                        $field = $this->_filtersMap[$field];
                    }

                    $collection->addFieldToFilter($field, $value);
                }
            } catch (Mage_Core_Exception $e) {
                $this->_fault('filters_invalid', $e->getMessage());
            }
        }

        $result = array();

        foreach ($collection as $product) {
//            $result[] = $product->getData();
            $result[] = array( // Basic product data
                'product_id' => $product->getId(),
                'sku'        => $product->getSku(),
                'name'       => $product->getName(),
                'set'        => $product->getAttributeSetId(),
                'type'       => $product->getTypeId(),
                'category_ids'       => $product->getCategoryIds()
            );
        }

        return $result;
    }

    /**
     * Retrieve product info
     *
     * @param int|string $productId
     * @param string|int $store
     * @param array $attributes
     * @return array
     */
    public function info($productId, $store = null, $attributes = null, $identifierType = null)
    {
        $product = $this->_getProduct($productId, $store, $identifierType);


        $result = array( // Basic product data
            'product_id' => $product->getId(),
            'sku'        => $product->getSku(),
            'set'        => $product->getAttributeSetId(),
            'type'       => $product->getTypeId(),
            'categories' => $product->getCategoryIds(),
            'websites'   => $product->getWebsiteIds()
        );

        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $attributes)) {
                $result[$attribute->getAttributeCode()] = $product->getData(
                                                                $attribute->getAttributeCode());
            }
        }

        return $result;
    }

    /**
     * Create new product.
     *
     * @param string $type
     * @param int $set
     * @param string $sku
     * @param array $productData
     * @param string $store
     * @return int
     */
    public function create($type, $set, $sku, $productData, $store = null)
    {
        if (!$type || !$set || !$sku) {
            $this->_fault('data_invalid');
        }

        $this->_checkProductTypeExists($type);
        $this->_checkProductAttributeSet($set);

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product');
        $product->setStoreId($this->_getStoreId($store))
            ->setAttributeSetId($set)
            ->setTypeId($type)
            ->setSku($sku);

        if (!isset($productData['stock_data']) || !is_array($productData['stock_data'])) {
            //Set default stock_data if not exist in product data
            $product->setStockData(array('use_config_manage_stock' => 0));
        }

        foreach ($product->getMediaAttributes() as $mediaAttribute) {
            $mediaAttrCode = $mediaAttribute->getAttributeCode();
            $product->setData($mediaAttrCode, 'no_selection');
        }

        $this->_prepareDataForSave($product, $productData);

        try {
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             * @todo see Mage_Catalog_Model_Product::validate()
             */
            if (is_array($errors = $product->validate())) {
                $strErrors = array();
                foreach($errors as $code => $error) {
                    if ($error === true) {
                        $error = Mage::helper('catalog')->__('Attribute "%s" is invalid.', $code);
                    }
                    $strErrors[] = $error;
                }
                $this->_fault('data_invalid', implode("\n", $strErrors));
            }

            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $product->getId();
    }

    /**
     * Update product data
     *
     * @param int|string $productId
     * @param array $productData
     * @param string|int $store
     * @return boolean
     */
    public function update($productId, $productData, $store = null, $identifierType = null)
    {
        $product = $this->_getProduct($productId, $store, $identifierType);

        $this->_prepareDataForSave($product, $productData);

        try {
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             * @todo see Mage_Catalog_Model_Product::validate()
             */
            if (is_array($errors = $product->validate())) {
                $strErrors = array();
                foreach($errors as $code => $error) {
                    if ($error === true) {
                        $error = Mage::helper('catalog')->__('Value for "%s" is invalid.', $code);
                    } else {
                        $error = Mage::helper('catalog')->__('Value for "%s" is invalid: %s', $code, $error);
                    }
                    $strErrors[] = $error;
                }
                $this->_fault('data_invalid', implode("\n", $strErrors));
            }

            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    /**
     *  Set additional data before product saved
     *
     *  @param    Mage_Catalog_Model_Product $product
     *  @param    array $productData
     *  @return   object
     */
    protected function _prepareDataForSave($product, $productData)
    {
        if (isset($productData['website_ids']) && is_array($productData['website_ids'])) {
            $product->setWebsiteIds($productData['website_ids']);
        }

        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            //Unset data if object attribute has no value in current store
            if (Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID !== (int) $product->getStoreId()
                && !$product->getExistsStoreValueFlag($attribute->getAttributeCode())
                && !$attribute->isScopeGlobal()
            ) {
                $product->setData($attribute->getAttributeCode(), false);
            }

            if ($this->_isAllowedAttribute($attribute)) {
                if (isset($productData[$attribute->getAttributeCode()])) {
                    $product->setData(
                        $attribute->getAttributeCode(),
                        $productData[$attribute->getAttributeCode()]
                    );
                } elseif (isset($productData['additional_attributes']['single_data'][$attribute->getAttributeCode()])) {
                    $product->setData(
                        $attribute->getAttributeCode(),
                        $productData['additional_attributes']['single_data'][$attribute->getAttributeCode()]
                    );
                } elseif (isset($productData['additional_attributes']['multi_data'][$attribute->getAttributeCode()])) {
                    $product->setData(
                        $attribute->getAttributeCode(),
                        $productData['additional_attributes']['multi_data'][$attribute->getAttributeCode()]
                    );
                }
            }
        }

        if (isset($productData['categories']) && is_array($productData['categories'])) {
            $product->setCategoryIds($productData['categories']);
        }

        if (isset($productData['websites']) && is_array($productData['websites'])) {
            foreach ($productData['websites'] as &$website) {
                if (is_string($website)) {
                    try {
                        $website = Mage::app()->getWebsite($website)->getId();
                    } catch (Exception $e) { }
                }
            }
            $product->setWebsiteIds($productData['websites']);
        }

        if (Mage::app()->isSingleStoreMode()) {
            $product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
        }

        if (isset($productData['stock_data']) && is_array($productData['stock_data'])) {
            $product->setStockData($productData['stock_data']);
        }

        if (isset($productData['tier_price']) && is_array($productData['tier_price'])) {
             $tierPrices = Mage::getModel('catalog/product_attribute_tierprice_api')
                 ->prepareTierPrices($product, $productData['tier_price']);
             $product->setData(Mage_Catalog_Model_Product_Attribute_Tierprice_Api::ATTRIBUTE_CODE, $tierPrices);
        }
    }

    /**
     * Update product special price
     *
     * @param int|string $productId
     * @param float $specialPrice
     * @param string $fromDate
     * @param string $toDate
     * @param string|int $store
     * @return boolean
     */
    public function setSpecialPrice($productId, $specialPrice = null, $fromDate = null, $toDate = null, $store = null)
    {
        return $this->update($productId, array(
            'special_price'     => $specialPrice,
            'special_from_date' => $fromDate,
            'special_to_date'   => $toDate
        ), $store);
    }

    /**
     * Retrieve product special price
     *
     * @param int|string $productId
     * @param string|int $store
     * @return array
     */
    public function getSpecialPrice($productId, $store = null)
    {
        return $this->info($productId, $store, array('special_price', 'special_from_date', 'special_to_date'));
    }

    /**
     * Delete product
     *
     * @param int|string $productId
     * @return boolean
     */
    public function delete($productId, $identifierType = null)
    {
        $product = $this->_getProduct($productId, null, $identifierType);

        try {
            $product->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }

        return true;
    }

   /**
    * Get list of additional attributes which are not in default create/update list
    *
    * @param  $productType
    * @param  $attributeSetId
    * @return array
    */
    public function getAdditionalAttributes($productType, $attributeSetId)
    {
        $this->_checkProductTypeExists($productType);
        $this->_checkProductAttributeSet($attributeSetId);

        /** @var $product Mage_Catalog_Model_Product */
        $productAttributes = Mage::getModel('catalog/product')
            ->setAttributeSetId($attributeSetId)
            ->setTypeId($productType)
            ->getTypeInstance(false)
            ->getEditableAttributes();

        $result = array();
        foreach ($productAttributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if ($attribute->isInSet($attributeSetId) && $this->_isAllowedAttribute($attribute)
                && !in_array($attribute->getAttributeCode(), $this->_defaultProductAttributeList)) {

                if ($attribute->isScopeGlobal()) {
                    $scope = 'global';
                } elseif ($attribute->isScopeWebsite()) {
                    $scope = 'website';
                } else {
                    $scope = 'store';
                }

                $result[] = array(
                    'attribute_id' => $attribute->getId(),
                    'code' => $attribute->getAttributeCode(),
                    'type' => $attribute->getFrontendInput(),
                    'required' => $attribute->getIsRequired(),
                    'scope' => $scope
                );
            }
        }

        return $result;
    }

    /**
     * Check if product type exists
     *
     * @param  $productType
     * @throw Mage_Api_Exception
     * @return void
     */
    protected function _checkProductTypeExists($productType)
    {
        if (!in_array($productType, array_keys(Mage::getModel('catalog/product_type')->getOptionArray()))) {
            $this->_fault('product_type_not_exists');
        }
    }

    /**
     * Check if attributeSet is exits and in catalog_product entity group type
     *
     * @param  $attributeSetId
     * @throw Mage_Api_Exception
     * @return void
     */
    protected function _checkProductAttributeSet($attributeSetId)
    {
        $attributeSet = Mage::getModel('eav/entity_attribute_set')->load($attributeSetId);
        if (is_null($attributeSet->getId())) {
            $this->_fault('product_attribute_set_not_exists');
        }
        if (Mage::getModel('catalog/product')->getResource()->getTypeId() != $attributeSet->getEntityTypeId()) {
            $this->_fault('product_attribute_set_not_valid');
        }
    }

	/**
	 * Custom method
	 * Gaurav Pant, 8th Sept 2013
	 * get all attributes in product listing
	 */
	public function getAllProducts($filters = null, $store = null, $attributes = null, $identifierType = null) {
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
		return $result;
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
} // Class Mage_Catalog_Model_Product_Api End
