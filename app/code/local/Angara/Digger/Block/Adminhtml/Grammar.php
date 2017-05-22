<?php
class Angara_Digger_Block_Adminhtml_Grammar extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
     //where is the controller
     $this->_controller = 'adminhtml_grammar';
     $this->_blockGroup = 'digger';
     //text in the admin header
     $this->_headerText = 'Grammar';
     //value of the add button
     $this->_addButtonLabel = 'Add a Grammar Rule';
     parent::__construct();
     }
}
?>