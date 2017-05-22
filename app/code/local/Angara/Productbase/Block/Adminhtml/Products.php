<?php
class Angara_Productbase_Block_Adminhtml_Products extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
    {
     //where is the controller
     $this->_controller = 'adminhtml_products';
     $this->_blockGroup = 'productbase';
 
     //text in the admin header
     $this->_headerText = 'Angara productbase management';
 
     //value of the add button
     $this->_addButtonLabel = 'Add a product for pricing';
	 
	 $this->_addButton('reset_pricing', array(
                'label'     => "Reset Prices",
                'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/resetprices') . '\')',
            ));
 
     parent::__construct();
     }
    
}
