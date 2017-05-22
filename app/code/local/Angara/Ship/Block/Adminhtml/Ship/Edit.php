<?php
class Angara_Ship_Block_Adminhtml_Ship_Edit extends Mage_Adminhtml_Block_Widget_Form_Container{
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = "ship_id";
		$this->_blockGroup = "ship";
		$this->_controller = "adminhtml_ship";
		$this->_updateButton("save", "label", Mage::helper("ship")->__("Save Item"));
		$this->_updateButton("delete", "label", Mage::helper("ship")->__("Delete Item"));

		$this->_addButton("saveandcontinue", array(
			"label"     => Mage::helper("ship")->__("Save And Continue Edit"),
			"onclick"   => "saveAndContinueEdit()",
			"class"     => "save",
		), -100);

		$this->_formScripts[] = 	"function saveAndContinueEdit(){
										editForm.submit($('edit_form').action+'back/edit/');
									}";
	}

	public function getHeaderText()
	{
		if( Mage::registry("ship_data") && Mage::registry("ship_data")->getId() ){
			return Mage::helper("ship")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("ship_data")->getId()));
		} 
		else{
			 return Mage::helper("ship")->__("Add Item");
		}
	}
}