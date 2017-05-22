<?php

class Angara_Promotions_Block_Adminhtml_Channel_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("channelGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("promotions/channel")->getCollection();
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
                
				$this->addColumn("name", array(
				"header" => Mage::helper("promotions")->__("Channel Title"),
				"index" => "name",
				));
				$this->addColumn("url_identifier", array(
				"header" => Mage::helper("promotions")->__("Url Identifier String"),
				"index" => "url_identifier",
				));
						$this->addColumn('status', array(
						'header' => Mage::helper('promotions')->__('Status'),
						'index' => 'status',
						'type' => 'options',
						'options'=>Angara_Promotions_Block_Adminhtml_Channel_Grid::getOptionArray2(),				
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
			$this->getMassactionBlock()->addItem('remove_channel', array(
					 'label'=> Mage::helper('promotions')->__('Remove Channel'),
					 'url'  => $this->getUrl('*/adminhtml_channel/massRemove'),
					 'confirm' => Mage::helper('promotions')->__('Are you sure?')
				));
			return $this;
		}
			
		static public function getOptionArray2()
		{
            $data_array=array(); 
			$data_array[0]='Disabled';
			$data_array[1]='Enabled';
            return($data_array);
		}
		static public function getValueArray2()
		{
            $data_array=array();
			foreach(Angara_Promotions_Block_Adminhtml_Channel_Grid::getOptionArray2() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		static public function getBanners($activeOnly = false){
			$data_array=array('No Selection');
			foreach(Mage::getModel('promotions/banner')->getBanners($activeOnly) as $banner){
               $data_array[]=array('value'=>$banner->getId(),'label'=>$banner->getName());
			}
            return($data_array);
		}

}