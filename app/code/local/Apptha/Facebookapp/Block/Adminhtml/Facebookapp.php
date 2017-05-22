<?php
/**
 * @name         :  Magento Facebook App
 * @version      :  1.1
 * @since        :  Magento 1.4
 * @author       :  Apptha - http://www.apptha.com
 * @copyright    :  Copyright (C) 2011 Powered by Apptha
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  6 October 2011
 * 
 * */
class Apptha_Facebookapp_Block_Adminhtml_Facebookapp extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_facebookapp';
    $this->_blockGroup = 'facebookapp';
    $this->_headerText = Mage::helper('facebookapp')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('facebookapp')->__('Add Item');
    parent::__construct();
  }
}