<?php

class Angara_Card_Block_Adminhtml_Card_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("cardGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("card/card")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("card")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("customer_name", array(
				"header" => Mage::helper("card")->__("Customer Name"),
				"index" => "customer_name",
				'frame_callback' => array($this, 'callback_Field')
				));
				$this->addColumn("customer_email", array(
				"header" => Mage::helper("card")->__("Customer Email"),
				"index" => "customer_email",
				'frame_callback' => array($this, 'callback_Field')
				));
				$this->addColumn("c_number", array(
				"header" => Mage::helper("card")->__("Card Number"),
				"index" => "c_number",
				'frame_callback' => array($this, 'callback_Field'),
				));
				/*$this->addColumn("c_exp_month", array(
				"header" => Mage::helper("card")->__("Expiry Month"),
				"index" => "c_exp_month",
				));
				$this->addColumn("c_exp_year", array(
				"header" => Mage::helper("card")->__("Expiry Year"),
				"index" => "c_exp_year",
				));
				$this->addColumn("c_cvv", array(
				"header" => Mage::helper("card")->__("CVV"),
				"index" => "c_cvv",
				));*/

				return parent::_prepareColumns();
		}
		
		public function callback_Field($value, $row, $column, $isExport) {
			$value	=	Mage::helper('card')->decrypt_cont($value);
			if($column->getIndex() == 'c_number'){
				$value = str_repeat("*", strlen($value) - 4) . substr($value, -4, 4);	
			}
		   	return $value;
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_card', array(
					 'label'=> Mage::helper('card')->__('Remove Card'),
					 'url'  => $this->getUrl('*/adminhtml_card/massRemove'),
					 'confirm' => Mage::helper('card')->__('Are you sure?')
				));
			return $this;
		}
			
		static public function getOptionArray2()
		{
            $data_array=array(); 
			$data_array[0]='-- Please Select Card Type --';
			$data_array[1]='Visa';
			$data_array[2]='Master';
			$data_array[3]='Discover';
			$data_array[4]='American Express';
            return($data_array);
		}
		static public function getValueArray2()
		{
            $data_array=array();
			foreach(Angara_Card_Block_Adminhtml_Card_Grid::getOptionArray2() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getOptionArray3(){
			$data_array			=	array();
			// set start and end month range
			$monthArrayNumber 	= 	range(1, 12);
			$monthArrayName 	= 	array('January','February','March','April','May','June','July','August','September','October','November','December');
			$data_array[0] 		= 	'-- Please Select Month --';
			foreach($monthArrayNumber as $key => $month) {
				$data_array[$key + 1] = ((($month < 10) ? '0'.$month : $month).' - '.$monthArrayName[$key]);
			}
			return($data_array);
		}
		
		static public function getValueArray3()
		{
            $data_array=array();
			foreach(Angara_Card_Block_Adminhtml_Card_Grid::getOptionArray3() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getOptionArray4()
		{
            $data_array=array(); 
			$currentYear = Mage::getModel('core/date')->date('Y');
			// set start and end year range
			$yearArray = range($currentYear, $currentYear + 10);
			$data_array[0] = '-- Please Select Year --';
			foreach ($yearArray as $key=>$year) {
				$data_array[$key + 1] = $year;
			}			
			return($data_array);
		}
		static public function getValueArray4()
		{
            $data_array=array();
			foreach(Angara_Card_Block_Adminhtml_Card_Grid::getOptionArray4() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
}