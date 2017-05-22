<?php
class Angara_Ship_Block_Adminhtml_Ship_Grid extends Mage_Adminhtml_Block_Widget_Grid{
	public function __construct()
	{
		parent::__construct();
		$this->setId("shipGrid");
		$this->setDefaultSort("ship_id");
		$this->setDefaultDir("DESC");
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel("ship/ship")->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn("ship_id", array(
				"header" => Mage::helper("ship")->__("ID"),
				"align" =>"right",
				"width" => "50px",
				"type" => "number",
				"index" => "ship_id",
		));
		
		$this->addColumn("name", array(
				"header" => Mage::helper("ship")->__("Name"),
				"index" => "name",
		));
		
		$this->addColumn("sort_order", array(
				"header" => Mage::helper("ship")->__("Sort Order"),
				"index" => "sort_order",
		));
		
		$this->addColumn('enabled', array(
			'header' => Mage::helper('ship')->__('Enabled'),
			'index' => 'enabled',
			'type' => 'options',
			'options'=>Angara_Ship_Block_Adminhtml_Ship_Grid::getOptionArray3(),				
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
		$this->setMassactionIdField('ship_id');
		$this->getMassactionBlock()->setFormFieldName('ship_ids');
		$this->getMassactionBlock()->setUseSelectAll(true);
		$this->getMassactionBlock()->addItem('remove_ship', array(
				 'label'=> Mage::helper('ship')->__('Remove Ship'),
				 'url'  => $this->getUrl('*/adminhtml_ship/massRemove'),
				 'confirm' => Mage::helper('ship')->__('Are you sure?')
			));
		return $this;
	}
		
	static public function getOptionArray3()
	{
		$data_array=array(); 
		$data_array[1]='Yes';
		$data_array[0]='No';
		return($data_array);
	}
	
	static public function getValueArray3()
	{
		$data_array=array();
		foreach(Angara_Ship_Block_Adminhtml_Ship_Grid::getOptionArray3() as $k=>$v){
		   $data_array[]=array('value'=>$k,'label'=>$v);		
		}
		return($data_array);
	}
}