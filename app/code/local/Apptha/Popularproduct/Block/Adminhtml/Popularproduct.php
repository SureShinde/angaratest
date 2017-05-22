<?php
class Apptha_Popularproduct_Block_Adminhtml_Popularproduct extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_popularproduct';
    $this->_blockGroup = 'popularproduct';
    $this->_headerText = Mage::helper('popularproduct')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('popularproduct')->__('Add Item');
    parent::__construct();
  }
}