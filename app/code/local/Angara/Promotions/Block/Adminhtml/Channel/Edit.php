<?php
	
class Angara_Promotions_Block_Adminhtml_Channel_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "promotions";
				$this->_controller = "adminhtml_channel";
				$this->_updateButton("save", "label", Mage::helper("promotions")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("promotions")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("promotions")->__("Save And Continue Edit"),
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
				if( Mage::registry("channel_data") && Mage::registry("channel_data")->getId() ){

				    return Mage::helper("promotions")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("channel_data")->getId()));

				} 
				else{

				     return Mage::helper("promotions")->__("Add Item");

				}
		}
}