<?php
class Angara_Arrivaldate_Block_Adminhtml_Daterules extends Mage_Adminhtml_Block_Widget_Grid_Container
{	
    public function __construct()
    {	
     //where is the controller
     $this->_controller = 'adminhtml_daterules';
     $this->_blockGroup = 'arrivaldate';
     //text in the admin header
     $this->_headerText = 'Daterules';
     //value of the add button
     $this->_addButtonLabel = 'Add new date rule';
     parent::__construct();
     }
}
?>