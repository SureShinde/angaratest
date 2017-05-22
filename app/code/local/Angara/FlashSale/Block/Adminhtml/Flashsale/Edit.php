<?php
	
class Angara_FlashSale_Block_Adminhtml_Flashsale_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "flashsale_id";
				$this->_blockGroup = "flashsale";
				$this->_controller = "adminhtml_flashsale";
				$this->_updateButton("save", "label", Mage::helper("flashsale")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("flashsale")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("flashsale")->__("Save And Continue Edit"),
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
				if( Mage::registry("flashsale_data") && Mage::registry("flashsale_data")->getId() ){

				    return Mage::helper("flashsale")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("flashsale_data")->getId()));

				} 
				else{

				     return Mage::helper("flashsale")->__("Add Item");

				}
		}
}