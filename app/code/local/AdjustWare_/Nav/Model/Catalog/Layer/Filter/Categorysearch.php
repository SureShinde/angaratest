<?php
/**
 * Product:     Layered Navigation Pro for Magento 20/04/11
 * Package:     AdjustWare_Nav_2.1.1_0.1.4_8_74006
 * Purchase ID: QgBMpw5YKeeQ8cDxt9xj7DfR5rzlpaEBHoIuAZvOxv
 * Generated:   2011-06-08 07:09:19
 * File path:   app/code/local/AdjustWare/Nav/Model/Catalog/Layer/Filter/Categorysearch.php
 * Copyright:   (c) 2011 AITOC, Inc.
 */
?>
<?php

class AdjustWare_Nav_Model_Catalog_Layer_Filter_Categorysearch extends Mage_Catalog_Model_Layer_Filter_Category
{
    protected function _getItemsData()
    {
        if (!isset($queryStr))
		{
			$queryStr = '';
		}
		$key = $this->getLayer()->getStateKey().'_SEARCH_SUBCATEGORIES';
        $key .= Mage::helper('adjnav')->getCacheKey('cat');
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            $category   = $this->getCategory();
            
            /** @var $categoty Mage_Catalog_Model_Categeory */
            $categories = $category->getChildrenCategories();

            $data = array();
            $level = 0;
//            $parentId = 0;
            if ($category->getLevel() > 1){ // current category is not root
                $parent = $category->getParentCategory();
                
                ++$level;
                if ($parent->getLevel()>1){
                    $data[] = array(
                        'label' => $parent->getName(),
                        'value' => $parent->getId(),
                        'count' => 0,
                        'level' => $level,
                        'uri'   => $queryStr,
                    );
//                    $parentId = $parent->getId();
//                    $categories->addItem($parent);
                    
                }
                //always include current category
                ++$level;
                $data[] = array(
                    'label' => $category->getName(),
                    'value' => '',
                    'level' => $level,
                    'is_current' => true,
                    'uri'   => $queryStr,
                );
            }
             
            $this->getLayer()->getProductCollection()
                ->addCountToCategories($categories);
                
//            if ($parentId){
//                $data[0]['count'] = $parent->getProductCount();
//                $categories->removeItemByKey($parentId);
//            }    
            
            ++$level;
            foreach ($categories as $cat) {
                if ($cat->getIsActive() && $cat->getProductCount()) {
                     $data[] = array( 
                        'label'       => $cat->getName(),
                        'value'       => $cat->getId(), 
                        'count'       => $cat->getProductCount(),
                        'level'       => $level,
                        'category_id' => $cat->getId(),
                        'uri'         => $cat->getUrl(),
                    );
                }
            }
            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
    if (Mage::getStoreConfig('design/adjnav/reset_filters'))
    {
        $queryStr = '';
    }
    $pageKey  = Mage::getBlockSingleton('page/html_pager')->getPageVarName();
    $queryStr =  Mage::helper('adjnav')->getParams(true, $pageKey);            
            for ($i=0, $n=sizeof($data); $i<$n; ++$i) {
                $url = $data[$i]['uri'];
                $pos = strpos($url, '?');
                if ($pos)
                    $url = substr($url, 0, $pos);
                $data[$i]['uri'] = $url . $queryStr;
            }
        return $data;
    }

    protected function _initItems()
    {
        $data  = $this->_getItemsData();
        $items = array();
        foreach ($data as $itemData) {
            $obj = Mage::getModel('catalog/layer_filter_item');
            $obj->setData($itemData);
            $obj->setFilter($this);
            
            $items[] = $obj;
        }
        $this->_items = $items;
        return $this;
    }    
} 