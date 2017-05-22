<?php
/**
 * Product:     Layered Navigation Pro for Magento 20/04/11
 * Package:     AdjustWare_Nav_2.1.1_0.1.4_8_74006
 * Purchase ID: QgBMpw5YKeeQ8cDxt9xj7DfR5rzlpaEBHoIuAZvOxv
 * Generated:   2011-06-08 07:09:19
 * File path:   app/code/local/AdjustWare/Nav/Model/Catalog/Layer/Filter/Attribute.php
 * Copyright:   (c) 2011 AITOC, Inc.
 */
?>
<?php
class AdjustWare_Nav_Model_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Attribute
{
    public function __construct()
    {
        parent::__construct();
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = Mage::helper('adjnav')->getParam($this->_requestVar);
        $filter = explode('-', $filter);
        
        $ids = array();    
        foreach ($filter as $id){
            $id = intVal($id);
            if ($id)
                $ids[] = $id;    
        } 
        if ($ids){
            $this->applyMultipleValuesFilter($ids);     
        }
        
        $this->setActiveState($ids);
        return $this;
    }

    // copied from catalogindex
    protected function applyMultipleValuesFilter($ids)
    {
        $collection = $this->getLayer()->getProductCollection();
        $attribute  = $this->getAttributeModel();
        $table = Mage::getSingleton('core/resource')->getTableName('catalogindex/eav'); //check for prefix
		
        $alias = 'attr_index_'.$attribute->getId();
        $collection->getSelect()->join(
            array($alias => $table),
            $alias.'.entity_id=e.entity_id',
            array()
        )
     	->where($alias.'.store_id = ?', Mage::app()->getStore()->getId())
        ->where($alias.'.attribute_id = ?', $attribute->getId())
		->where($alias.'.value IN (?)', $ids);
        
        switch (Mage::getStoreConfig('design/adjnav/filtering_logic'))
        {
        	case 'AND':
        		if (is_array($ids) && ($size = count($ids)))
        		{
        			$adapter = $collection->getConnection();
        			$adapter = $collection->getConnection();
        			$subQuery = new Varien_Db_Select($adapter);
        			$subQuery
        				->from(Mage::getResourceModel('catalogindex/attribute')->getMainTable(), 'entity_id')
        				->where('store_id = ?', Mage::app()->getStore()->getId())
				        ->where('attribute_id = ?', $attribute->getId())
						->where('value IN (?)', $ids)
						->group(array('entity_id', 'attribute_id', 'store_id'))
						->having($size . ' = COUNT(value)');

					$res = $adapter->fetchCol($subQuery);
					
					if ($res)
					{
						$collection->getSelect()->where($alias . '.entity_id IN (?)', $res);
					}
					else
					{
						$collection->getSelect()->where($alias . '.entity_id IN (-1)');
					}
					
        		}
        		break;
        	case 'OR':
        	default:
        		break;
        }
        
        if (count($ids)>1)
        {
            $collection->getSelect()->distinct(true); 
        }
		
		$configProduct = Mage::getModel('catalog/product_type_configurable');
        
		if($attribute->getAttributeCode() == 'filterable_metal_types'){
			
			$attributeOptionsModel= Mage::getModel('eav/entity_attribute_source_table') ;
			$attributeOptionsModel->setAttribute($attribute);
			$options = $attributeOptionsModel->getAllOptions(false);
			foreach($options as $option) {
				//if ($option['label'] == 'Yellow Gold') {
					if(in_array($option['value'], $ids)){
						$metalType = $option['label'];
					}
				//}
				//else{
					//$yellowGoldOptionValue = $option['value'];
				//}
			}
			
			if(isset($metalType)){
				Mage::register('filtered_metal', $metalType);
			}
		}
		
		if($attribute->getAttributeCode() == 'filterable_stone_grades'){
			
			$attributeOptionsModel= Mage::getModel('eav/entity_attribute_source_table') ;
			$attributeOptionsModel->setAttribute($attribute);
			$options = $attributeOptionsModel->getAllOptions(false);
			foreach($options as $option) {
					if(in_array($option['value'], $ids)){
						$grade = $option['label'];
					}
			}

			if(isset($grade)){
				Mage::register('filtered_grade', $grade);
			}
		}
		
        return $this;
    }   
    
    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {        
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        $key = $this->getLayer()->getStateKey();
        $key .= Mage::helper('adjnav')->getCacheKey($this->_requestVar);

        $data = $this->getLayer()->getAggregator()->getCacheData($key);
        
        

        if ($data === null) {
            $data = array();

            $options = $attribute->getFrontend()->getSelectOptions();                        

            $optionsCount = Mage::getSingleton('catalogindex/attribute')->getCount(
                $attribute,
                $this->_getBaseCollectionSql()
            );

            foreach ($options as $option) {
                if (is_array($option['value'])) {
                    continue;
                }
                if (Mage::helper('core/string')->strlen($option['value'])) {
                    // Check filter type
                    if ($attribute->getIsFilterable() == self::OPTIONS_ONLY_WITH_RESULTS) {
                        if (!empty($optionsCount[$option['value']])) {
                            $data[] = array(
                                'label' => $option['label'],
                                'value' => $option['value'],
                                'count' => $optionsCount[$option['value']],
                            );
                        }
                    }
                    else {
                        $data[] = array(
                            'label' => $option['label'],
                            'value' => $option['value'],
                            'count' => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0,
                        );
                    }
                }
            }

            $currentIds = Mage::helper('adjnav')->getParam($attribute->getAttributeCode());
            $tags = array(
                Mage_Eav_Model_Entity_Attribute::CACHE_TAG . ':' . $currentIds,
            );

            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }
    
    protected function _getBaseCollectionSql()
    {        
        $alias = 'attr_index_' . $this->getAttributeModel()->getId();         
        // Varien_Db_Select
        $baseSelect = clone parent::_getBaseCollectionSql();
        
        // 1) remove from conditions
        $oldWhere = $baseSelect->getPart(Varien_Db_Select::WHERE);
        $newWhere = array();

        foreach ($oldWhere as $cond){
           if (!strpos($cond, $alias))
               $newWhere[] = $cond;
        }
  
        if ($newWhere && substr($newWhere[0], 0, 3) == 'AND')
           $newWhere[0] = substr($newWhere[0],3);        
        
        $baseSelect->setPart(Varien_Db_Select::WHERE, $newWhere);
        
        // 2) remove from joins
        $oldFrom = $baseSelect->getPart(Varien_Db_Select::FROM);
        $newFrom = array();
        
        foreach ($oldFrom as $name=>$val){
           if ($name != $alias)
               $newFrom[$name] = $val;
        }
        //it assumes we have at least one table 
        $baseSelect->setPart(Varien_Db_Select::FROM, $newFrom);        

        return $baseSelect;
    }
    
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getResourceModel('catalog/layer_filter_attribute');
        }
        return $this->_resource;
    }
    
} 