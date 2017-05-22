<?php


class Angara_Ship_Block_Adminhtml_Ship extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_ship";
	$this->_blockGroup = "ship";
	$this->_headerText = Mage::helper("ship")->__("Shipping Methods Manager");
	$this->_addButtonLabel = Mage::helper("ship")->__("Add New Item");
	parent::__construct();
	
	}

}