<?php

class Angara_Feedback_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
	{
			parent::__construct();
			$this->setId("categoryGrid");
			$this->setDefaultSort("category_id");
			$this->setDefaultDir("DESC");
			$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
			$collection = Mage::getModel("feedback/category")->getCollection();
			$this->setCollection($collection);
			return parent::_prepareCollection();
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn("category_id", array(
			"header" => Mage::helper("feedback")->__("ID"),
			"align" =>"right",
			"width" => "50px",
			"type" => "number",
			"index" => "category_id",
		));
		
		$this->addColumn('parent_category_id', array(
			'header' => Mage::helper('feedback')->__('Parent Category'),
			'index' => 'parent_category_id',
			'type' => 'options',
			'options'=>Angara_Feedback_Block_Adminhtml_Category_Grid::getOptionArray2(),				
		));
		
		$this->addColumn("name", array(
			"header" => Mage::helper("feedback")->__("Category Name"),
			"index" => "name",
		));
		
		$this->addColumn('status', array(
			'header' => Mage::helper('feedback')->__('Status'),
			'index' => 'status',
			'type' => 'options',
			'options'=>Angara_Feedback_Block_Adminhtml_Category_Grid::getOptionArray6(),				
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
		$this->setMassactionIdField('category_id');
		$this->getMassactionBlock()->setFormFieldName('category_ids');
		$this->getMassactionBlock()->setUseSelectAll(true);
		$this->getMassactionBlock()->addItem('remove_feedback', array(
			 'label'=> Mage::helper('feedback')->__('Remove Category'),
			 'url'  => $this->getUrl('*/adminhtml_feedback/massRemove'),
			 'confirm' => Mage::helper('feedback')->__('Are you sure?')
		));
		return $this;
	}
		
	static public function getOptionArray2()
	{
		$data_array		=	array(); 
		$currentItemId 	= 	Mage::app()->getRequest()->getParam('id');
		//	Find the active parent categories
		$collection 	= 	Mage::getModel("feedback/category")->getCollection()
								->addFieldToSelect('*')
								->addFieldToFilter('status', array('eq' => '1'))
								->addFieldToFilter('parent_category_id', array('eq' => '0'))
								->addFieldToFilter('category_id', array('neq' => $currentItemId))
								->setOrder('name',ASC)
								->load();
								//->load(1);die;
		//echo '<pre>';print_r($collection->getdata());die;
		$data_array[0]	=	'None';
		if( $collection->count() ){
			foreach($collection as $_method) {
				$data_array[$_method->getData('category_id')] = 	$_method->getData('name');
			}
		}
		return($data_array);
	}
	
	static public function getValueArray2()
	{
		$data_array=array();
		foreach(Angara_Feedback_Block_Adminhtml_Category_Grid::getOptionArray2() as $k=>$v){
		   $data_array[]=array('value'=>$k,'label'=>$v);		
		}
		return($data_array);

	}
	
	static public function getOptionArray6()
	{
		$data_array=array(); 
		$data_array[0]='Disabled';
		$data_array[1]='Enabled';
		return($data_array);
	}
	
	static public function getValueArray6()
	{
		$data_array=array();
		foreach(Angara_Feedback_Block_Adminhtml_Category_Grid::getOptionArray6() as $k=>$v){
		   $data_array[]=array('value'=>$k,'label'=>$v);		
		}
		return($data_array);

	}
}