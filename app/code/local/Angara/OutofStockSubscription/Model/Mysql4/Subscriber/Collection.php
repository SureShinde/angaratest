<?php

/**
 * @category   Angara
 * @package    Angara_OutofStockSubscription
 * @author     Asheesh Singh
 */
class Angara_OutofStockSubscription_Model_Mysql4_Subscriber_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Initialize collection
     *
     */
    protected function _construct()
    {
        $this->_init('outofstocksubscription/subscriber');   
    }

    protected function _initSelect()
    { 
    	parent::_initSelect();
    	$this->getSelect()->join(array("cpe" => 'catalog_product_entity'), 'main_table.product_id=cpe.entity_id', array('sku'=>'cpe.sku'));
    }
}
