<?php
/**
 * @category   Angara
 * @package    Angara_Gifts
 * @copyright  Copyright (c) 2014 Angara eCommerce (http://www.angara.com)
 * @license    http://angara.com/LICENSE-COMMUNITY.txt
 */
 
class Angara_Gifts_Block_Adminhtml_Gifts extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_gifts';
    $this->_blockGroup = 'gifts';
    $this->_headerText = Mage::helper('gifts')->__('Rules Manager');
    $this->_addButtonLabel = Mage::helper('gifts')->__('Add Rule');
    parent::__construct();
  }
}
