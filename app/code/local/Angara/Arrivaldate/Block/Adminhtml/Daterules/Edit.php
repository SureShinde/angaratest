<?php
class Angara_Arrivaldate_Block_Adminhtml_Daterules_Edit extends
                    Mage_Adminhtml_Block_Widget_Form_Container{
					
					
   public function __construct()
   {
        parent::__construct();
        $this->_objectId = 'id';
        //vwe assign the same blockGroup as the Grid Container
        $this->_blockGroup = 'arrivaldate';
        //and the same controller
        $this->_controller = 'adminhtml_daterules';
        //define the label for the save and delete button
        $this->_updateButton('save', 'label','save rule');
        $this->_updateButton('delete', 'label', 'delete rule');
		
		
    }
       /* Here, we're looking if we have transmitted a form object,
          to update the good text in the header of the page (edit or add) */
    public function getHeaderText()
    {
        if( Mage::registry('daterules_data')&&Mage::registry('daterules_data')->getId())
         {
              return 'Edit rule'.$this->htmlEscape(
              Mage::registry('daterules_data')->getTitle()).'<br />';
         }
         else
         {
             return 'Add a rule';
         }
    }
}