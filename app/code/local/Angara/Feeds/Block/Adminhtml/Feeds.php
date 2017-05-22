<?php
class Angara_Feeds_Block_Adminhtml_Feeds extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_feeds';
    $this->_blockGroup = 'feeds';
    $this->_headerText = Mage::helper('feeds')->__('Feeds Manager');
    $this->_addButtonLabel = Mage::helper('feeds')->__('Add Feeds');
    parent::__construct();
  }
}