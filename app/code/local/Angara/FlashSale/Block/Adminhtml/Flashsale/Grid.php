<?php
class Angara_FlashSale_Block_Adminhtml_Flashsale_Grid extends Mage_Adminhtml_Block_Widget_Grid{

	public function __construct()
	{
		parent::__construct();
		$this->setId("flashsaleGrid");
		$this->setDefaultSort("flashsale_id");
		$this->setDefaultDir("DESC");
		$this->setSaveParametersInSession(true);
		//$this->setTemplate('path/to/your/grid.phtml');
	}


	protected function _prepareCollection()
	{
		$collection = Mage::getModel("flashsale/flashsale")->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	
	protected function _prepareColumns()
	{
		$this->addColumn("flashsale_id", array(
			"header" 	=> Mage::helper("flashsale")->__("ID"),
			"align" 	=>"right",
			"width" 	=> "50px",
			"type" 		=> "number",
			"index" 	=> "flashsale_id",
		));
		
		$this->addColumn("flashsale_name", array(
			"header" 	=> Mage::helper("flashsale")->__("Name"),
			"index" 	=> "flashsale_name",
		));
		
		$this->addColumn('is_active', array(
			'header' 	=> Mage::helper('flashsale')->__('Enabled'),
			'index' 	=> 'is_active',
			'type' 		=> 'options',
			'options'	=>Angara_FlashSale_Block_Adminhtml_Flashsale_Grid::getOptionArray2(),				
		));
				
		$this->addColumn('from_date', array(
			'header'    => Mage::helper('flashsale')->__('From Date'),
			'index'     => 'from_date',
			'type'      => 'datetime',
		));
	
		$this->addColumn('to_date', array(
			'header'    => Mage::helper('flashsale')->__('To Date'),
			'index'     => 'to_date',
			'type'      => 'datetime',
		));
		
		$this->addColumn("product_id", array(
			"header" 	=> Mage::helper("flashsale")->__("SKU"),
			"index" 	=> "product_id",
			'renderer'  => 'Angara_FlashSale_Block_Adminhtml_Flashsale_Grid_Renderer_Red',		//	Changing the grid color
			//'renderer'=> new Angara_FlashSale_Block_Adminhtml_Flashsale_Grid_Renderer_Red(),
		));
			
		$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
		$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

		return parent::_prepareColumns();
	}


	public function getRowUrl($row)
	{
		   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
	}


	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('flashsale_id');
		$this->getMassactionBlock()->setFormFieldName('flashsale_ids');
		$this->getMassactionBlock()->setUseSelectAll(true);
		$this->getMassactionBlock()->addItem('remove_flashsale', array(
			 'label'=> Mage::helper('flashsale')->__('Remove Flashsale'),
			 'url'  => $this->getUrl('*/adminhtml_flashsale/massRemove'),
			 'confirm' => Mage::helper('flashsale')->__('Are you sure?')
		));
		return $this;
	}
		
		
	static public function getOptionArray2()
	{
		$data_array=array(); 
		$data_array[0]='Yes';
		$data_array[1]='No';
		return($data_array);
	}
	
	
	static public function getValueArray2()
	{
		$data_array=array();
		foreach(Angara_FlashSale_Block_Adminhtml_Flashsale_Grid::getOptionArray2() as $k=>$v){
		   $data_array[]=array('value'=>$k,'label'=>$v);		
		}
		return($data_array);

	}
}