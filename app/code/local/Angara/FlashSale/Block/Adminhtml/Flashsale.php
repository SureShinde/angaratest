<?php


class Angara_FlashSale_Block_Adminhtml_Flashsale extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_flashsale";
	$this->_blockGroup = "flashsale";
	$this->_headerText = Mage::helper("flashsale")->__("Flashsale Manager");
	$this->_addButtonLabel = Mage::helper("flashsale")->__("Add New Item");
	parent::__construct();
	
	}

}