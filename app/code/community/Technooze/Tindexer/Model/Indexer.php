<?php
/**
 * Technooze_Tindexer extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Technooze
 * @package    Technooze_Tindexer
 * @copyright  Copyright (c) 2008 Technooze LLC
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * @category Technooze
 * @package  Technooze_Tindexer
 * @module   Tindexer
 * @author   Damodar Bashyal (enjoygame @ hotmail.com)
 */
class Technooze_Tindexer_Model_Indexer extends Mage_Index_Model_Indexer_Abstract
{
    protected $_matchedEntities = array(
            'tindexer_entity' => array(
                Mage_Index_Model_Event::TYPE_SAVE
            )
        );

    // var to protect multiple runs
    protected $_registered = false;
    protected $_processed = false;
    protected $_categoryId = 0;
    protected $_productIds = array();

    /**
     * not sure why this is required.
     * _registerEvent is only called if this function is included.
     *
     * @param Mage_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        return Mage::getModel('catalog/category_indexer_product')->matchEvent($event);
    }


    public function getName(){
        //return Mage::helper('tindexer')->__('Nav Product Count');
		return Mage::helper('tindexer')->__('Catalog Price Range');
    }

    public function getDescription(){
        //return Mage::helper('tindexer')->__('Refresh Product count on left nav.');
		return Mage::helper('tindexer')->__('Rebuild product min max price for Catalog page.');
    }

    protected function _registerEvent(Mage_Index_Model_Event $event){
        // if event was already registered once, then no need to register again.
        if($this->_registered) return $this;

        $entity = $event->getEntity();
		//echo '<br>entity->'.$entity;die;
        switch ($entity) {
            /*case Mage_Catalog_Model_Product::ENTITY:
               $this->_registerProductEvent($event);
                break;

            case Mage_Catalog_Model_Category::ENTITY:
                $this->_registerCategoryEvent($event);
                break;*/

            case Mage_Catalog_Model_Convert_Adapter_Product::ENTITY:
                $event->addNewData('tindexer_indexer_reindex_all', true);
                break;

            case Mage_Core_Model_Store::ENTITY:
            case Mage_Core_Model_Store_Group::ENTITY:
                $process = $event->getProcess();
                $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                break;
        }
        $this->_registered = true;
        return $this;
    }

    /**
     * Register event data during product save process
     *
     * @param Mage_Index_Model_Event $event
     */
    /*protected function _registerProductEvent(Mage_Index_Model_Event $event)
    {
        $eventType = $event->getType();
        
        if ($eventType == Mage_Index_Model_Event::TYPE_SAVE || $eventType == Mage_Index_Model_Event::TYPE_MASS_ACTION) {
            $process = $event->getProcess();
            $this->_productIds = $event->getDataObject()->getData('product_ids');
            $this->flagIndexRequired($this->_productIds, 'products');

            $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }
    }*/

    /**
     * Register event data during category save process
     *
     * @param Mage_Index_Model_Event $event
     */
    /*protected function _registerCategoryEvent(Mage_Index_Model_Event $event)
    {
        $category = $event->getDataObject();
        
        
        if ($category->getIsChangedProductList() || $category->getAffectedCategoryIds()) {
            $process = $event->getProcess();
            $this->_categoryId = $event->getDataObject()->getData('entity_id');
            $this->flagIndexRequired($this->_categoryId, 'categories');

            $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }
    }*/
    protected function _processEvent(Mage_Index_Model_Event $event){
        // process index event
        if(!$this->_processed){
            $this->_processed = true;
        }
    }

    /*public function flagIndexRequired($ids=array(), $type='products'){
        $ids = (array)$ids;
        $collection = Mage::getModel('tindexer/products')->getCollection();
        $filter = array();
        foreach($ids as $id){
            $filter[] = array('like' => "%,{$id},%");
        }
        $collection->addFieldToFilter($type, $filter);
        $collection->setDataToAll('flag', 1);
        $collection->save();
    }*/

    public function reindexAll(){
        // reindex all data which are flagged 1 | initFilteredProductsCount
        /*$collection = Mage::getModel('tindexer/products')->getCollection()->addFieldToFilter('flag', 1);
        foreach($collection as $v){
            try{
                Mage::getModel('tindexer/products')->initFilteredProductsCount('brand', $v->getData('attr_id'), $v->getData('tindexer_id'));
            } catch (Exception $e) {
                Mage::log($e->getMessage());
                return;
            }
        }*/
		
		
		
		
		//	Getting list of all configurable products
		$collectionConfigurable = Mage::getResourceModel('catalog/product_collection')
									->addAttributeToFilter('status', 1)
									->addAttributeToFilter('type_id', array('eq' => 'configurable'))
									//->addAttributeToFilter('entity_id', array('eq' => '20188'))
									->addAttributeToSort('entity_id', 'DESC')
									//->load(1);die;
									;
		$totalConfigurableProductsCount	=	count($collectionConfigurable);
		if($totalConfigurableProductsCount>0){
			$childIdsArray	=	array();
			foreach ($collectionConfigurable as $_configurableproduct) {
				$configurableProductId	=	$_configurableproduct->getId(); 
				$childIds 				= 	Mage::getModel('catalog/product_type_configurable')
											->getChildrenIds($configurableProductId);	//	Get child products id (only ids)
				$childIdsArray			=	$childIds[0];
	
				$noOfAssociateProducts	=	count($childIdsArray); 
				if($noOfAssociateProducts>1){
					$firstElement	=	array_shift($childIdsArray);
					array_unshift($childIdsArray,$firstElement);				// Push the element on top of array
					$childPrice		=	array();
					foreach($childIdsArray as $child){
						$_child = Mage::getModel('catalog/product')->load($child);
						$childPrice[] =  $_child->getPrice();
					}
					//echo '<pre>'; print_r($childPrice); die;
					$_minimalPriceValue	=	min($childPrice);
					$_maxPriceValue		=	max($childPrice);
					//	Lets update the minimum price in DB
					$resource 	= 	Mage::getSingleton('core/resource');
					$write 		= 	$resource->getConnection('core_write');		// reading the database resource
					
					$updatefieldsData 		= 	array('min_price' => $_minimalPriceValue,'max_price' => $_maxPriceValue);
					$updateWhereCondition	=	" entity_id = '".$configurableProductId."' ";
					if(	$write->update('catalog_product_index_price', $updatefieldsData, $updateWhereCondition)	){
						//echo 'Record updated successfully for configurable product ID - '.$configurableProductId.' with minimum price '.$_minimalPriceValue.'<br>';
					}else{
						//echo 'Record not updated for configurable product ID - '.$configurableProductId.'<br>';
					}
				}	//	end if
			}	//	end foreach
		}	//	end if
	
		
    }
}