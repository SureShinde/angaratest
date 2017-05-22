<?php
class Angara_Digger_Block_Adminhtml_Synonym_Edit extends
                    Mage_Adminhtml_Block_Widget_Form_Container{
					
					
   public function __construct()
   {
        parent::__construct();
        $this->_objectId = 'id';
        //vwe assign the same blockGroup as the Grid Container
        $this->_blockGroup = 'digger';
        //and the same controller
        $this->_controller = 'adminhtml_synonym';
        //define the label for the save and delete button
        $this->_updateButton('save', 'label','save synonym');
        $this->_updateButton('delete', 'label', 'delete synonym');
		
		
    }
       /* Here, we're looking if we have transmitted a form object,
          to update the good text in the header of the page (edit or add) */
    public function getHeaderText()
    {
        if( Mage::registry('synonym_data')&&Mage::registry('synonym_data')->getId())
         {
              return 'Edit synonym'.$this->htmlEscape(
              Mage::registry('synonym_data')->getTitle()).'<br />';
         }
         else
         {
             return 'Add a synonym';
         }
    }
}