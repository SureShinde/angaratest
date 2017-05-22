<?php
class Angara_CustomerStory_Block_Adminhtml_Story_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId("storyGrid");
		$this->setDefaultSort("id");
		$this->setDefaultDir("DESC");
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel("customerstory/story")->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn("id", array(
		"header" => Mage::helper("customerstory")->__("ID"),
		"align" => "right",
		"width" => "10px",
		"type" => "number",
		"index" => "id",
		));
		
		$this->addColumn("order_id", array(
		"header" => Mage::helper("customerstory")->__("Order Number"),
		"index" => "order_id",
		"width" => "20px",
		));
		
		$this->addColumn("images", array(
		"header" => Mage::helper("customerstory")->__("Images"),
		"index" => "image_details",
		"align" => "left",
		"index" => "image",
		"filter" => false,
		"width" => "350px",
		"renderer" => "Angara_CustomerStory_Block_Adminhtml_Story_Grid_Image_Renderer",
		));
		
		$this->addColumn("description", array(
		"header" => Mage::helper("customerstory")->__("Text"),
		"index" => "description",
		"width" => "450px",
		));
		
		$this->addColumn('is_approved', array(
		'header' => Mage::helper('customerstory')->__('Approved'),
		"index" => "is_approved",
		"type" => "options",
		"width" => "35px",
		"options" => Angara_CustomerStory_Block_Adminhtml_Story_Grid::getOptionArray3(),				
		));
					
		$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
		$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

		return parent::_prepareColumns();
	}

	public function getRowUrl($row)
	{
		return $this->getUrl("*/*/edit", array("id" => $row->getId()));
	}
	
	static public function getOptionArray3()
	{
		$data_array=array(); 
		$data_array[0]='No';
		$data_array[1]='Yes';
		return($data_array);
	}
	
	static public function getValueArray3()
	{
		$data_array=array();
		foreach(Angara_CustomerStory_Block_Adminhtml_Story_Grid::getOptionArray3() as $k=>$v){
			$data_array[]=array('value'=>$k,'label'=>$v);		
		}
		return($data_array);
	}
}?>