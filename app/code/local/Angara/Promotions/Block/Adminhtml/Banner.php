<?php


class Angara_Promotions_Block_Adminhtml_Banner extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_banner";
	$this->_blockGroup = "promotions";
	$this->_headerText = Mage::helper("promotions")->__("Banner Manager");
	$this->_addButtonLabel = Mage::helper("promotions")->__("Add New Item");
	parent::__construct();
	
	}

}