<?php

class Angara_Promotions_Block_Adminhtml_Banner_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("bannerGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("promotions/banner")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("promotions")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                $this->addColumn("unique_identifier", array(
				"header" => Mage::helper("promotions")->__("Unique Identifier"),
				"index" => "unique_identifier",
				));
				$this->addColumn("name", array(
				"header" => Mage::helper("promotions")->__("Banner Title"),
				"index" => "name",
				));
				$this->addColumn("url", array(
				"header" => Mage::helper("promotions")->__("Redirect Url"),
				"index" => "url",
				));
						$this->addColumn('status', array(
						'header' => Mage::helper('promotions')->__('Status'),
						'index' => 'status',
						'type' => 'options',
						'options'=>Angara_Promotions_Block_Adminhtml_Banner_Grid::getOptionArray2(),				
						));
						
				$this->addColumn("image_title", array(
				"header" => Mage::helper("promotions")->__("Title Text"),
				"index" => "image_title",
				));
				$this->addColumn("image_alt_text", array(
				"header" => Mage::helper("promotions")->__("Alt Text"),
				"index" => "image_alt_text",
				));
						$this->addColumn('has_ticker', array(
						'header' => Mage::helper('promotions')->__('Allow Ticker'),
						'index' => 'has_ticker',
						'type' => 'options',
						'options'=>Angara_Promotions_Block_Adminhtml_Banner_Grid::getOptionArray8(),				
						));
						
					$this->addColumn('created_at', array(
						'header'    => Mage::helper('promotions')->__('created_at'),
						'index'     => 'created_at',
						'type'      => 'datetime',
					));
					$this->addColumn('updated_at', array(
						'header'    => Mage::helper('promotions')->__('updated_at'),
						'index'     => 'updated_at',
						'type'      => 'datetime',
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
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_banner', array(
					 'label'=> Mage::helper('promotions')->__('Remove Banner'),
					 'url'  => $this->getUrl('*/adminhtml_banner/massRemove'),
					 'confirm' => Mage::helper('promotions')->__('Are you sure?')
				));
			return $this;
		}
			
		static public function getOptionArray2()
		{
            $data_array=array(); 
			$data_array[0]='Inactive';
			$data_array[1]='Active';
            return($data_array);
		}
		static public function getValueArray2()
		{
            $data_array=array();
			foreach(Angara_Promotions_Block_Adminhtml_Banner_Grid::getOptionArray2() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getOptionArray8()
		{
            $data_array=array(); 
			$data_array[0]='No';
			$data_array[1]='Yes';
            return($data_array);
		}
		static public function getValueArray8()
		{
            $data_array=array();
			foreach(Angara_Promotions_Block_Adminhtml_Banner_Grid::getOptionArray8() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}