<?php
	
class Angara_Feedback_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "feedback_id";
				$this->_blockGroup = "feedback";
				$this->_controller = "adminhtml_category";
				$this->_updateButton("save", "label", Mage::helper("feedback")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("feedback")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("feedback")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("category_data") && Mage::registry("category_data")->getId() ){

				    return Mage::helper("feedback")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("category_data")->getId()));

				} 
				else{

				     return Mage::helper("feedback")->__("Add Item");

				}
		}
}