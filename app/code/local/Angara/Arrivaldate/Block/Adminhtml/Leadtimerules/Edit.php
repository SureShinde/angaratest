<?php
class Angara_Arrivaldate_Block_Adminhtml_Leadtimerules_Edit extends
                    Mage_Adminhtml_Block_Widget_Form_Container{
					
					
   public function __construct()
   {
        parent::__construct();
        $this->_objectId = 'id';
        //vwe assign the same blockGroup as the Grid Container
        $this->_blockGroup = 'arrivaldate';
        //and the same controller
        $this->_controller = 'adminhtml_leadtimerules';
        //define the label for the save and delete button
        $this->_updateButton('save', 'label','save leadtime rule');
        $this->_updateButton('delete', 'label', 'delete leadtime rule');
		
		
    }
       /* Here, we're looking if we have transmitted a form object,
          to update the good text in the header of the page (edit or add) */
    public function getHeaderText()
    {
        if( Mage::registry('leadtimerules_data')&&Mage::registry('leadtimerules_data')->getId())
         {
              return 'Edit leadtimerules'.$this->htmlEscape(
              Mage::registry('leadtimerules_data')->getTitle()).'<br />';
         }
         else
         {
             return 'Add a leadtime rule';
         }
    }
}