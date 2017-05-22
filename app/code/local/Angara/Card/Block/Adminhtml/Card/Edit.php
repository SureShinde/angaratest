<?php
	
class Angara_Card_Block_Adminhtml_Card_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "card";
				$this->_controller = "adminhtml_card";
				$this->_updateButton("save", "label", Mage::helper("card")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("card")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("card")->__("Save And Continue Edit"),
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
				if( Mage::registry("card_data") && Mage::registry("card_data")->getId() ){

				    return Mage::helper("card")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("card_data")->getId()));

				} 
				else{

				     return Mage::helper("card")->__("Add Item");

				}
		}
}