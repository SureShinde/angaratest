<?php
class Angara_Productbase_Block_Adminhtml_Stones extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
    {
     //where is the controller
     $this->_controller = 'adminhtml_stones';
     $this->_blockGroup = 'productbase';
 
     //text in the admin header
     $this->_headerText = 'Angara productbase management';
	 
	 # todo add export csv feature
 
     parent::__construct();
	 $this->removeButton("add");
     }
    
}
