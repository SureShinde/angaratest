<?php
class Studs_Diamondstud_Block_Adminhtml_Diamondstud extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_diamondstud';
    $this->_blockGroup = 'diamondstud';
    $this->_headerText = Mage::helper('diamondstud')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('diamondstud')->__('Add Item');
    parent::__construct();
  }
}