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
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * CatalogSearch Fulltext Index resource model
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
 
class Mage_CatalogSearch_Model_Resource_Fulltext extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Searchable attributes cache
     *
     * @var array
     */
    protected $_searchableAttributes     = null;

    /**
     * Index values separator
     *
     * @var string
     */
    protected $_separator                = '|SALT|';

    /**
     * Array of Zend_Date objects per store
     *
     * @var array
     */
    protected $_dates                    = array();

    /**
     * Product Type Instances cache
     *
     * @var array
     */
    protected $_productTypes             = array();

    /**
     * Store search engine instance
     *
     * @var object
     */
    protected $_engine                   = null;

    /**
     * Whether table changes are allowed
     *
     * @deprecated after 1.6.1.0
     * @var bool
     */
    protected $_allowTableChanges       = true;



	protected $_completedIds = array();

    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('catalogsearch/fulltext', 'product_id');
        $this->_engine = Mage::helper('catalogsearch')->getEngine();
    }

    /**
     * Return options separator
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->_separator;
    }

    /**
     * Regenerate search index for store(s)
     *
     * @param  int|null $storeId
     * @param  int|array|null $productIds
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    public function rebuildIndex($storeId = null, $productIds = null)
    {
        if (is_null($storeId)) {
            $storeIds = array_keys(Mage::app()->getStores());
            foreach ($storeIds as $storeId) {
                $this->_rebuildStoreIndex($storeId, $productIds);
            }
        } else {
            $this->_rebuildStoreIndex($storeId, $productIds);
        }

        return $this;
    }

    /**
     * Regenerate search index for specific store
     *
     * @param int $storeId Store View Id
     * @param int|array $productIds Product Entity Id
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    protected function _rebuildStoreIndex($storeId, $productIds = null)
    {
        //$this->cleanIndex($storeId, $productIds);
        // prepare searchable attributes
        $staticFields = array();
        foreach ($this->_getSearchableAttributes('static') as $attribute) {
            $staticFields[] = $attribute->getAttributeCode();
        }
		$staticFields[] = 'updated_at';
        $dynamicFields = array(
            'int'       => array_keys($this->_getSearchableAttributes('int')),
            'varchar'   => array_keys($this->_getSearchableAttributes('varchar')),
            'text'      => array_keys($this->_getSearchableAttributes('text')),
            'decimal'   => array_keys($this->_getSearchableAttributes('decimal')),
            'datetime'  => array_keys($this->_getSearchableAttributes('datetime')),
        );
	
        // status and visibility filter
        $visibility     = $this->_getSearchableAttribute('visibility');
        $status         = $this->_getSearchableAttribute('status');
        $statusVals     = Mage::getSingleton('catalog/product_status')->getVisibleStatusIds();
        $allowedVisibilityValues = $this->_engine->getAllowedVisibility();
		$searchWeightModel = $this->_getSearchableAttribute('searchindex_weight');

        $lastProductId = '0000-00-00 00:00:00';
		while (true) {
            $products = $this->_getSearchableProducts($storeId, $staticFields, $productIds, $lastProductId);
            if (!$products) {
                break;
            }
			$searchableProductIds = array();
            $productAttributes = array();
            $productRelations  = array();
            foreach ($products as $productData) {
				$this->_completedIds[] = (int) $productData['entity_id'];
                $searchableProductIds[] = (int) $productData['entity_id'];
				$lastProductId = $productData['updated_at'];
                $productAttributes[$productData['entity_id']] = $productData['entity_id'];
                $productChildren = $this->_getProductChildIds($productData['entity_id'], $productData['type_id']);
				//$productChildren = array();
                $productRelations[$productData['entity_id']] = $productChildren;
                if ($productChildren) {
                    foreach ($productChildren as $productChildId) {
                        $productAttributes[$productChildId] = $productChildId;
                    }
                }
            }
			
			/*foreach($productRelations as $parentId => $productChildren){
				foreach ($productChildren as $productChildId) {
					
				}
			}*/
			
			$productTags = $this->_getProductTags($searchableProductIds);

            $productIndexes    = array();
            $productAttributes = $this->_getProductAttributes($storeId, $productAttributes, $dynamicFields);
            foreach ($products as $productData) {
				
				$indexSet = array();
				
                if (!isset($productAttributes[$productData['entity_id']])) {
                    continue;
                }

                $productAttr = $productAttributes[$productData['entity_id']];
                if (!isset($productAttr[$visibility->getId()])
                    || !in_array($productAttr[$visibility->getId()], $allowedVisibilityValues)
                ) {
                    continue;
                }
                if (!isset($productAttr[$status->getId()]) || !in_array($productAttr[$status->getId()], $statusVals)) {
                    continue;
                }
				
                $productIndex = array(
                    $productData['entity_id'] => $productAttr
                );
				
				$tags = NULL;
				if(isset($productTags[$productData['entity_id']])){
					$tags = $productTags[$productData['entity_id']];
				}

                $index = $this->_prepareProductIndex($productIndex, $productData, $storeId, $tags);
				$searchWeight = $productAttr[$searchWeightModel->getId()];
				
				if(empty($searchWeight)) $searchWeight = 9999;
				$productIndexes[$productData['entity_id']]['data'] = $index;
				$productIndexes[$productData['entity_id']]['search_weight'] = $searchWeight;
				$productIndexes[$productData['entity_id']]['updated_at'] = $productData['updated_at'];
				$indexSet = explode($this->_separator, $index);
				
				if ($productChildren = $productRelations[$productData['entity_id']]) {
                    foreach ($productChildren as $productChildId) {
                        if (isset($productAttributes[$productChildId])) {
                            //$productIndex[$productChildId] = $productAttributes[$productChildId];
							$subProductIndex = array(
								$productChildId => $productAttr
							);
							$subProductIndex[$productChildId] = $productAttributes[$productChildId];
							$subIndex = explode($this->_separator, $this->_prepareProductIndex($subProductIndex, $productData, $storeId, $tags));
							$subIndex = array_diff($subIndex, $indexSet);
							$indexSet = array_merge($indexSet, $subIndex);
							$subIndex = Mage::helper('catalogsearch')->prepareIndexdata($subIndex, $this->_separator);
							
							$productIndexes[$productChildId]['data'] = $subIndex;
							$productIndexes[$productChildId]['search_weight'] = $searchWeight;
							$productIndexes[$productChildId]['updated_at'] = $productIndexes[$productData['entity_id']]['updated_at'];
                        }
                    }
                }
            }
			/* Mage::log(print_r($productIndexes,true),NULL,'index-log.log',true); */
            $this->_saveProductIndexes($storeId, $productIndexes);
        }

        $this->resetSearchResults();

        return $this;
    }

    /**
     * Retrieve searchable products per store
     *
     * @param int $storeId
     * @param array $staticFields
     * @param array|int $productIds
     * @param int $lastProductId
     * @param int $limit
     * @return array
     */
    protected function _getSearchableProducts($storeId, array $staticFields, $productIds = null, $lastProductId = '0000-00-00 00:00:00',
        $limit = 100)
    {
        $websiteId      = Mage::app()->getStore($storeId)->getWebsiteId();
        $writeAdapter   = $this->_getWriteAdapter();
		//echo get_class($writeAdapter->select());die;
        $select = $writeAdapter->select()
            ->useStraightJoin(true)
            ->from(
                array('e' => $this->getTable('catalog/product')),
                array_merge(array('entity_id', 'type_id'), $staticFields)
            )
            ->join(
                array('website' => $this->getTable('catalog/product_website')),
                $writeAdapter->quoteInto(
                    'website.product_id=e.entity_id AND website.website_id=?',
                    $websiteId
                ),
                array()
            )
            ->join(
                array('stock_status' => $this->getTable('cataloginventory/stock_status')),
                $writeAdapter->quoteInto(
                    'stock_status.product_id=e.entity_id AND stock_status.website_id=?',
                    $websiteId
                ),
                array('in_stock' => 'stock_status')
            )
			->joinLeft(
                array('fulltext' => $this->getTable('catalogsearch/fulltext')),
                $writeAdapter->quoteInto(
                    'fulltext.product_id=e.entity_id AND fulltext.store_id=?',
                    $storeId
                ),
				array('fulltext_id'=>'fulltext_id','index_date'=>'updated_at','fulltextID'=>'fulltext_id')
                //array("(CASE when fulltext.updated_at IS NULL then 0 ELSE fulltext.updated_at END) AS index_date")
			);
		// exclude already updated products
		$select->where('fulltext.updated_at IS NULL OR fulltext.updated_at < e.updated_at');
		
        if (!is_null($productIds)) {
            $select->where('e.entity_id IN(?)', $productIds);
        }
		
		// exclude child products
		$select->where('e.entity_id NOT IN (SELECT `child_id` FROM (`catalog_product_relation`))');		
		if(count($this->_completedIds) > 0) $condpage = 'e.entity_id NOT IN ('.implode(',',$this->_completedIds).')';
		else $condpage = '1';
		$select->where($condpage)
            ->limit($limit)
            ->order('e.updated_at DESC');
		//if(count($this->_completedIds) > 0){
			//echo $select;die;
		//}
		/* Mage::log($select->__toString(),null,'query-index.log'); */
        $result = $writeAdapter->fetchAll($select);

        return $result;
    }

    /**
     * Reset search results
     *
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    public function resetSearchResults()
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->update($this->getTable('catalogsearch/search_query'), array('is_processed' => 0));
        $adapter->delete($this->getTable('catalogsearch/result'));

        Mage::dispatchEvent('catalogsearch_reset_search_result');

        return $this;
    }

    /**
     * Delete search index data for store
     *
     * @param int $storeId Store View Id
     * @param int $productId Product Entity Id
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    public function cleanIndex($storeId = null, $productId = null)
    {
        if ($this->_engine) {
            $this->_engine->cleanIndex($storeId, $productId);
        }

        return $this;
    }
	/**
     * Update search index updated_at for store
     *
     * @param int $storeId Store View Id
     * @param int $productId Product Entity Id
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    public function updateIndex($storeId = null, $productId = null)
    {
		if (!is_null($productId)) {
			$relations = $this->_getProductParentIds($productId);
			if(count($relations) > 0) {
				$parent = array();
				$child = array();
				foreach($relations as $relation){
					if(isset($relation['parent_id'])) $parent[] = $relation['parent_id'];
					if(isset($relation['child_id'])) $child[] = $relation['child_id'];
				}
				if(count($parent) > 0 || count($child) > 0) {
					if(count($parent) > 0) {
						$productId = array_merge($productId,$parent);
					}
					if(count($child) > 0) {
						$productId = array_diff($productId,$child);
					}
					$productId = array_unique($productId);
					$productId = array_values($productId);
				}
			}
		}
	
        if ($this->_engine) {
            $this->_engine->updateIndex($storeId, $productId);
        }

        return $this;
    }
	

    /**
     * Prepare results for query
     *
     * @param Mage_CatalogSearch_Model_Fulltext $object
     * @param string $queryText
     * @param Mage_CatalogSearch_Model_Query $query
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    public function prepareResult($object, $queryText, $query)
    {
        $adapter = $this->_getWriteAdapter();
        if (!$query->getIsProcessed()) {
		//if(true){
            $searchType = $object->getSearchType($query->getStoreId());

            $preparedTerms = Mage::getResourceHelper('catalogsearch')
                ->prepareTerms($queryText, $query->getMaxQueryWords());

            $bind = array();
            $like = array();
            $likeCond  = '';
            if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_LIKE
                || $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE
            ) {
                $helper = Mage::getResourceHelper('core');
                $words = Mage::helper('core/string')->splitWords($queryText, true, $query->getMaxQueryWords());
                foreach ($words as $word) {
                    $like[] = $helper->getCILike('s.data_index', $word, array('position' => 'any'));
                }
                if ($like) {
                    $likeCond = '(' . join(' OR ', $like) . ')';
                }
            }
            $mainTableAlias = 's';
            $fields = array(
                'query_id' => new Zend_Db_Expr($query->getId()),
                'product_id',
            );
            $select = $adapter->select()
                ->from(array($mainTableAlias => $this->getMainTable()), $fields)
                ->joinInner(array('e' => $this->getTable('catalog/product')),
                    'e.entity_id = s.product_id',
                    array())
                ->where($mainTableAlias.'.store_id = ?', (int)$query->getStoreId());

            if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_FULLTEXT
                || $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE
            ) {
                //$bind[':query'] = implode(' ', $preparedTerms[0]);
				$queryDetails = Mage::getResourceHelper('catalogsearch')->buildQueryDetails($preparedTerms[0], $this->_separator);
				$bind[':query'] = $queryDetails['generated_query'];
				if(!empty($queryDetails['refined_searches'])){
					$query->setRefinedSearches($queryDetails['refined_searches']);
				}
                $where = Mage::getResourceHelper('catalogsearch')
                    ->chooseFulltext($this->getMainTable(), $mainTableAlias, $select);
            }

            if ($likeCond != '' && $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE) {
                    $where .= ($where ? ' OR ' : '') . $likeCond;
            } elseif ($likeCond != '' && $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_LIKE) {
                $select->columns(array('relevance'  => new Zend_Db_Expr(0)));
                $where = $likeCond;
            }

            if ($where != '') {
                $select->where($where);
            }

            $sql = $adapter->insertFromSelect($select,
                $this->getTable('catalogsearch/result'),
                array(),
                Varien_Db_Adapter_Interface::INSERT_ON_DUPLICATE);
            $adapter->query($sql, $bind);

            $query->setIsProcessed(1);
        }

        return $this;
    }

    /**
     * Retrieve EAV Config Singleton
     *
     * @return Mage_Eav_Model_Config
     */
    public function getEavConfig()
    {
        return Mage::getSingleton('eav/config');
    }

    /**
     * Retrieve searchable attributes
     *
     * @param string $backendType
     * @return array
     */
    protected function _getSearchableAttributes($backendType = null)
    {
        if (is_null($this->_searchableAttributes)) {
            $this->_searchableAttributes = array();

            $productAttributeCollection = Mage::getResourceModel('catalog/product_attribute_collection');

            if ($this->_engine && $this->_engine->allowAdvancedIndex()) {
                $productAttributeCollection->addToIndexFilter(true);
            } else {
                $productAttributeCollection->addSearchableAttributeFilter();
            }
            $attributes = $productAttributeCollection->getItems();

            Mage::dispatchEvent('catelogsearch_searchable_attributes_load_after', array(
                'engine' => $this->_engine,
                'attributes' => $attributes
            ));

            $entity = $this->getEavConfig()
                ->getEntityType(Mage_Catalog_Model_Product::ENTITY)
                ->getEntity();

            foreach ($attributes as $attribute) {
                $attribute->setEntity($entity);
            }

            $this->_searchableAttributes = $attributes;
        }

        if (!is_null($backendType)) {
            $attributes = array();
            foreach ($this->_searchableAttributes as $attributeId => $attribute) {
                if ($attribute->getBackendType() == $backendType) {
                    $attributes[$attributeId] = $attribute;
                }
            }

            return $attributes;
        }

        return $this->_searchableAttributes;
    }

    /**
     * Retrieve searchable attribute by Id or code
     *
     * @param int|string $attribute
     * @return Mage_Eav_Model_Entity_Attribute
     */
    protected function _getSearchableAttribute($attribute)
    {
        $attributes = $this->_getSearchableAttributes();
        if (is_numeric($attribute)) {
            if (isset($attributes[$attribute])) {
                return $attributes[$attribute];
            }
        } elseif (is_string($attribute)) {
            foreach ($attributes as $attributeModel) {
                if ($attributeModel->getAttributeCode() == $attribute) {
                    return $attributeModel;
                }
            }
        }

        return $this->getEavConfig()->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attribute);
    }

    /**
     * Returns expresion for field unification
     *
     * @param string $field
     * @param string $backendType
     * @return Zend_Db_Expr
     */
    protected function _unifyField($field, $backendType = 'varchar')
    {
        if ($backendType == 'datetime') {
            $expr = Mage::getResourceHelper('catalogsearch')->castField(
                $this->_getReadAdapter()->getDateFormatSql($field, '%Y-%m-%d %H:%i:%s'));
        } else {
            $expr = Mage::getResourceHelper('catalogsearch')->castField($field);
        }
        return $expr;
    }

    /**
     * Load product(s) attributes
     *
     * @param int $storeId
     * @param array $productIds
     * @param array $attributeTypes
     * @return array
     */
    protected function _getProductAttributes($storeId, array $productIds, array $attributeTypes)
    {
        $result  = array();
        $selects = array();
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        $adapter = $this->_getWriteAdapter();
        $ifStoreValue = $adapter->getCheckSql('t_store.value_id > 0', 't_store.value', 't_default.value');
        foreach ($attributeTypes as $backendType => $attributeIds) {
            if ($attributeIds) {
                $tableName = $this->getTable(array('catalog/product', $backendType));
                $select = $adapter->select()
                    ->from(
                        array('t_default' => $tableName),
                        array('entity_id', 'attribute_id'))
                    ->joinLeft(
                        array('t_store' => $tableName),
                        $adapter->quoteInto(
                            't_default.entity_id=t_store.entity_id' .
                                ' AND t_default.attribute_id=t_store.attribute_id' .
                                ' AND t_store.store_id=?',
                            $storeId),
                        array('value' => $this->_unifyField($ifStoreValue, $backendType)))
                    ->where('t_default.store_id=?', 0)
                    ->where('t_default.attribute_id IN (?)', $attributeIds)
                    ->where('t_default.entity_id IN (?)', $productIds);

                /**
                 * Add additional external limitation
                 */
                Mage::dispatchEvent('prepare_catalog_product_index_select', array(
                    'select'        => $select,
                    'entity_field'  => new Zend_Db_Expr('t_default.entity_id'),
                    'website_field' => $websiteId,
                    'store_field'   => new Zend_Db_Expr('t_store.store_id')
                ));

                $selects[] = $select;
            }
        }

        if ($selects) {
            $select = $adapter->select()->union($selects, Zend_Db_Select::SQL_UNION_ALL);
            $query = $adapter->query($select);
            while ($row = $query->fetch()) {
                $result[$row['entity_id']][$row['attribute_id']] = $row['value'];
            }
        }

        return $result;
    }

    /**
     * Retrieve Product Type Instance
     *
     * @param string $typeId
     * @return Mage_Catalog_Model_Product_Type_Abstract
     */
    protected function _getProductTypeInstance($typeId)
    {
        if (!isset($this->_productTypes[$typeId])) {
            $productEmulator = $this->_getProductEmulator();
            $productEmulator->setTypeId($typeId);

            $this->_productTypes[$typeId] = Mage::getSingleton('catalog/product_type')
                ->factory($productEmulator);
        }
        return $this->_productTypes[$typeId];
    }

    /**
     * Return all product children ids
     *
     * @param $productId
     * @param $typeId
     * @param null|int $websiteId
     * @return array|null
     */
    protected function _getProductChildrenIds($productId, $typeId, $websiteId = null)
    {
        $typeInstance = $this->_getProductTypeInstance($typeId);
        $relation = $typeInstance->isComposite()
            ? $typeInstance->getRelationInfo()
            : false;

        if ($relation && $relation->getTable() && $relation->getParentFieldName() && $relation->getChildFieldName()) {
            $select = $this->_getReadAdapter()->select()
                ->from(
                    array('main' => $this->getTable($relation->getTable())),
                    array($relation->getChildFieldName()))
                ->where("main.{$relation->getParentFieldName()} = ?", $productId);
            if (!is_null($relation->getWhere())) {
                $select->where($relation->getWhere());
            }

            Mage::dispatchEvent('prepare_product_children_id_list_select', array(
                'select'        => $select,
                'entity_field'  => 'main.product_id',
                'website_field' => $websiteId
            ));

            return $this->_getReadAdapter()->fetchCol($select);
        }

        return null;
    }
	
	/**
     * Return all product parent ids
     *
     * @param int $childProductId Product Entity Id
     * @param string $typeId Super Product Link Type
     * @return array
     */
    protected function _getProductParentIds($childProductId)
    {
		$select = $this->_getReadAdapter()->select()
			->from(
				array('main' => 'catalog_product_relation'),
				array('parent_id'=>'parent_id','child_id'=>'child_id'))
			->where("main.child_id IN (?)", $childProductId);
	   
		return $this->_getReadAdapter()->fetchAll($select);
    }
	
	
	protected function _getProductTags(array $productIds)
    {
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$query = 'select t.tag_id, t.name, tr.product_id from `tag` as t
left join `tag_relation` as tr on tr.tag_id = t.tag_id
where t.status = 1 and tr.product_id in ('.implode(',', $productIds).')';
		$results = $readConnection->fetchAll($query);
	
		$tags = array();
		foreach($results as $result){
			$tags[$result['product_id']][$result['tag_id']] = $result['name'];
		}
        return $tags;
    }
	

    /**
     * Retrieve Product Emulator (Varien Object)
     *
     * @return Varien_Object
     */
    protected function _getProductEmulator()
    {
        $productEmulator = new Varien_Object();
        $productEmulator->setIdFieldName('entity_id');

        return $productEmulator;
    }

    /**
     * Prepare Fulltext index value for product
     *
     * @param array $indexData
     * @param array $productData
     * @param int $storeId
     * @return string
     */
    protected function _prepareProductIndex($indexData, $productData, $storeId, $tags)
    {
        $index = array();

        foreach ($this->_getSearchableAttributes('static') as $attribute) {
            $attributeCode = $attribute->getAttributeCode();

            if (isset($productData[$attributeCode])) {
                $value = $this->_getAttributeValue($attribute->getId(), $productData[$attributeCode], $storeId);
                if ($value) {
                    //For grouped products
                    if (isset($index[$attributeCode])) {
                        if (!is_array($index[$attributeCode])) {
                            $index[$attributeCode] = array($index[$attributeCode]);
                        }
                        $index[$attributeCode][] = $value;
                    }
                    //For other types of products
                    else {
                        $index[$attributeCode] = $value;
                    }
                }
            }
        }
        foreach ($indexData as $entityId => $attributeData) {
            foreach ($attributeData as $attributeId => $attributeValue) {
                $value = $this->_getAttributeValue($attributeId, $attributeValue, $storeId);
                if (!is_null($value) && $value !== false) {
                    $attributeCode = $this->_getSearchableAttribute($attributeId)->getAttributeCode();

                    if (isset($index[$attributeCode])) {
                        $index[$attributeCode][$entityId] = $value;
                    } else {
                        //$index[$attributeCode] = array($entityId => $value);
						$index[$attributeCode] = $value;
                    }
                }
            }
        }

        if (!$this->_engine->allowAdvancedIndex()) {
            $product = $this->_getProductEmulator()
                ->setId($productData['entity_id'])
                ->setTypeId($productData['type_id'])
                ->setStoreId($storeId);
            $typeInstance = $this->_getProductTypeInstance($productData['type_id']);
            if ($data = $typeInstance->getSearchableData($product)) {
                $index['options'] = $data;
            }
        }
		
		if(isset($tags)){
			$index['tags'] = $tags;
		}

        /*if (isset($productData['in_stock'])) {
            $index['in_stock'] = $productData['in_stock'];
        }*/

        if ($this->_engine) {
            return $this->_engine->prepareEntityIndex($index, $this->_separator);
        }

        return Mage::helper('catalogsearch')->prepareIndexdata($index, $this->_separator);
    }

    /**
     * Retrieve attribute source value for search
     *
     * @param int $attributeId
     * @param mixed $value
     * @param int $storeId
     * @return mixed
     */
    protected function _getAttributeValue($attributeId, $value, $storeId)
    {
        $attribute = $this->_getSearchableAttribute($attributeId);
        if (!$attribute->getIsSearchable()) {
            if ($this->_engine->allowAdvancedIndex()) {
                if ($attribute->getAttributeCode() == 'visibility') {
                    return $value;
                } elseif (!($attribute->getIsVisibleInAdvancedSearch()
                    || $attribute->getIsFilterable()
                    || $attribute->getIsFilterableInSearch()
                    || $attribute->getUsedForSortBy())
                ) {
                    return null;
                }
            } else {
                return null;
            }
        }

        if ($attribute->usesSource()) {
            if ($this->_engine->allowAdvancedIndex()) {
                return $value;
            }

            $attribute->setStoreId($storeId);
            $value = $attribute->getSource()->getIndexOptionText($value);

            if (is_array($value)) {
                $value = implode($this->_separator, $value);
            } elseif (empty($value)) {
                $inputType = $attribute->getFrontend()->getInputType();
                if ($inputType == 'select' || $inputType == 'multiselect') {
                    return null;
                }
            }
        } elseif ($attribute->getBackendType() == 'datetime') {
            $value = $this->_getStoreDate($storeId, $value);
        } else {
            $inputType = $attribute->getFrontend()->getInputType();
            if ($inputType == 'price') {
                $value = Mage::app()->getStore($storeId)->roundPrice($value);
            }
        }

        $value = preg_replace("#\s+#siu", ' ', trim(strip_tags($value)));

        return $value;
    }

    /**
     * Save Product index
     *
     * @param int $productId
     * @param int $storeId
     * @param string $index
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    protected function _saveProductIndex($productId, $storeId, $index)
    {
        if ($this->_engine) {
            $this->_engine->saveEntityIndex($productId, $storeId, $index);
        }

        return $this;
    }

    /**
     * Save Multiply Product indexes
     *
     * @param int $storeId
     * @param array $productIndexes
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    protected function _saveProductIndexes($storeId, $productIndexes)
    {
        if ($this->_engine) {
            $this->_engine->saveEntityIndexes($storeId, $productIndexes);
        }

        return $this;
    }

    /**
     * Retrieve Date value for store
     *
     * @param int $storeId
     * @param string $date
     * @return string
     */
    protected function _getStoreDate($storeId, $date = null)
    {
        if (!isset($this->_dates[$storeId])) {
            $timezone = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE, $storeId);
            $locale   = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeId);
            $locale   = new Zend_Locale($locale);

            $dateObj = new Zend_Date(null, null, $locale);
            $dateObj->setTimezone($timezone);
            $this->_dates[$storeId] = array($dateObj, $locale->getTranslation(null, 'date', $locale));
        }

        if (!is_empty_date($date)) {
            list($dateObj, $format) = $this->_dates[$storeId];
            $dateObj->setDate($date, Varien_Date::DATETIME_INTERNAL_FORMAT);

            return $dateObj->toString($format);
        }

        return null;
    }





    // Deprecated methods

    /**
     * Set whether table changes are allowed
     *
     * @deprecated after 1.6.1.0
     * @param bool $value
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    public function setAllowTableChanges($value = true)
    {
        $this->_allowTableChanges = $value;
        return $this;
    }

    /**
     * Update category products indexes
     *
     * deprecated after 1.6.2.0
     *
     * @param array $productIds
     * @param array $categoryIds
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    public function updateCategoryIndex($productIds, $categoryIds)
    {
        return $this;
    }

	/**
     * Return all product children ids
     *
     * @param int $productId Product Entity Id
     * @param string $typeId Super Product Link Type
     * @return array|null
     */
    protected function _getProductChildIds($productId, $typeId)
    {
        return $this->_getProductChildrenIds($productId, $typeId);
    }
}
