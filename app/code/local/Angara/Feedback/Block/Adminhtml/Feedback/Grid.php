<?php
class Angara_Feedback_Block_Adminhtml_Feedback_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
			parent::__construct();
			$this->setId("feedbackGrid");
			$this->setDefaultSort("feedback_id");
			$this->setDefaultDir("DESC");
			$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
			$collection = Mage::getModel("feedback/feedback")->getCollection();
			$this->setCollection($collection);
			return parent::_prepareCollection();
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn("feedback_id", array(
			"header" => Mage::helper("feedback")->__("ID"),
			"align" =>"right",
			"width" => "50px",
			"type" => "number",
			"index" => "feedback_id",
		));
		
		$this->addColumn("email", array(
			"header" => Mage::helper("feedback")->__("Email"),
			"index" => "email",
		));
		
		$this->addColumn("contact_number", array(
			"header" => Mage::helper("feedback")->__("Contact Number"),
			"index" => "contact_number",
		));
		
		$this->addColumn('category_id', array(
			'header' => Mage::helper('feedback')->__('Category'),
			'index' => 'category_id',
			'type' => 'options',
			'options'=>Angara_Feedback_Block_Adminhtml_Feedback_Grid::getOptionArray1(),				
		));
		
		$this->addColumn('sub_category_id', array(
			'header' => Mage::helper('feedback')->__('Sub Category'),
			'index' => 'sub_category_id',
			'type' => 'options',
			'options'=>Angara_Feedback_Block_Adminhtml_Feedback_Grid::getOptionArray2(),				
		));
		
		$this->addColumn('status', array(
			'header' => Mage::helper('feedback')->__('Status'),
			'index' => 'status',
			'type' => 'options',
			'options'=>Angara_Feedback_Block_Adminhtml_Feedback_Grid::getOptionArray5(),				
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
		$this->setMassactionIdField('feedback_id');
		$this->getMassactionBlock()->setFormFieldName('feedback_ids');
		$this->getMassactionBlock()->setUseSelectAll(true);
		$this->getMassactionBlock()->addItem('remove_feedback', array(
			 'label'=> Mage::helper('feedback')->__('Remove Feedback'),
			 'url'  => $this->getUrl('*/adminhtml_feedback/massRemove'),
			 'confirm' => Mage::helper('feedback')->__('Are you sure?')
		));
		return $this;
	}
		
	static public function getOptionArray1()
	{
		$data_array		=	array(); 
		//	Find the active parent categories
		$collection 	= 	Mage::getModel("feedback/category")->getCollection()
								->addFieldToSelect('*')
								->addFieldToFilter('status', array('eq' => '1'))
								->addFieldToFilter('parent_category_id', array('eq' => '0'))
								->setOrder('name',ASC)
								->load();
								//->load(1);die;
		//echo '<pre>';print_r($collection->getdata());die;
		//$data_array[0]	=	'None';
		if( $collection->count() ){
			foreach($collection as $_method) {
				$data_array[$_method->getData('category_id')] = 	$_method->getData('name');
			}
		}
		return($data_array);
	}
	
	static public function getValueArray1()
	{
		$data_array=array();
		foreach(Angara_Feedback_Block_Adminhtml_Feedback_Grid::getOptionArray1() as $k=>$v){
		   $data_array[]=array('value'=>$k,'label'=>$v);		
		}
		return($data_array);
	}
	
	static public function getOptionArray2()
	{
		$data_array		=	array(); 
		//	Find the active child categories
		$collection 	= 	Mage::getModel("feedback/category")->getCollection()
								->addFieldToSelect('*')
								//->addFieldToFilter('status', array('eq' => '1'))
								->addFieldToFilter('parent_category_id', array('neq' => '0'))
								//->addFieldToFilter('category_id', array('eq' => $categoryId))
								->setOrder('name',ASC)
								->load();
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
		foreach(Angara_Feedback_Block_Adminhtml_Feedback_Grid::getOptionArray2() as $k=>$v){
		   $data_array[]=array('value'=>$k,'label'=>$v);		
		}
		return($data_array);
	}
	
	static public function getOptionArray5()
	{
		$data_array=array(); 
		$data_array[0]='Disabled';
		$data_array[1]='Enabled';
		return($data_array);
	}
	
	static public function getValueArray5()
	{
		$data_array=array();
		foreach(Angara_Feedback_Block_Adminhtml_Feedback_Grid::getOptionArray5() as $k=>$v){
		   $data_array[]=array('value'=>$k,'label'=>$v);		
		}
		return($data_array);
	}
}