<?php 

/**
 * @rewrite by Asheesh
 */ 
 
class Angara_UtilityBackend_Block_Rewrite_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{

    protected function _prepareCollection()
    {	
        $collection = Mage::getResourceModel($this->_getCollectionClass());
		// Angara Modification Start
		$collection->getSelect()->joinLeft('sales_flat_order_oktoship', 'sales_flat_order_oktoship.orderid=main_table.entity_id');
		//echo "<pre>";echo($collection->getSelect());echo "</pre>";
		// Angara Modification End
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();
		$this->addColumnAfter('oktoship', array(
            'header' => Mage::helper('sales')->__('OK TO SHIP'),
            'index' => 'oktoship',
			'width' => '1',
        ),'grand_total');
		
        return $this;
    }
}
