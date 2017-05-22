<?php
class Angara_customerreview_Block_Adminhtml_Orderreview extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {

    $this->_controller = 'adminhtml_orderreview';
    $this->_blockGroup = 'customerreview';
    $this->_headerText = 'Order Review Management';


    parent::__construct();
	$this->_removeButton('add');	
	
  }
}
