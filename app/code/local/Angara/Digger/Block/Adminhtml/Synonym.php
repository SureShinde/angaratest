<?php
class Angara_Digger_Block_Adminhtml_Synonym extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
     //where is the controller
     $this->_controller = 'adminhtml_synonym';
     $this->_blockGroup = 'digger';
     //text in the admin header
     $this->_headerText = 'Synonyms management';
     //value of the add button
     $this->_addButtonLabel = 'Add a synonym';
     parent::__construct();
	 
	  /*
	 	Adding new button to admin grid
	 */
	 $this->_addButton('button_id', array(
						'label'     => Mage::helper('digger')->__('Import Synonyms'),
						//'onclick'   => 'jsfunction(this.id)',
						'onclick' => "setLocation('{$this->getUrl('*/*/import')}')",		//	admin action url
						'class'     => 'go'
					), 0, 100, 'header', 'header');
    
     }
}
?>