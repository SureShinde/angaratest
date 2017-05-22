<?php

class Angara_Promotions_Block_Adminhtml_Coupon_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("couponGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("promotions/coupon")->getCollection();
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
				
				//	S:VA
				$this->addColumn('rule_valid_for', array(
					'header' => Mage::helper('promotions')->__('Rule Valid For'),
					'index' => 'rule_valid_for',
					'type' => 'options',
					'options'=>Angara_Promotions_Block_Adminhtml_Coupon_Grid::getOptionArray10(),				
				));
				//	E:VA
                
						$this->addColumn('rule_id', array(
						'header' => Mage::helper('promotions')->__('Rule'),
						'index' => 'rule_id',
						'type' => 'options',
						'options'=>Angara_Promotions_Block_Adminhtml_Coupon_Grid::getOptionArray3_2(),				
						));
						
						$this->addColumn('valid_shipping', array(
						'header' => Mage::helper('promotions')->__('Shipping'),
						'index' => 'valid_shipping',
						'type' => 'options',
						'options'=>Angara_Promotions_Block_Adminhtml_Coupon_Grid::getOptionArray16(),				
						));
						
						$this->addColumn('channel_id', array(
						'header' => Mage::helper('promotions')->__('Channel'),
						'index' => 'channel_id',
						'type' => 'options',
						'options'=>Angara_Promotions_Block_Adminhtml_Coupon_Grid::getOptionArray4(),				
						));
						
				$this->addColumn("priority", array(
				"header" => Mage::helper("promotions")->__("Priority"),
				"index" => "priority",
				));
						$this->addColumn('display_on_frontend', array(
						'header' => Mage::helper('promotions')->__('Allow On Frontend'),
						'index' => 'display_on_frontend',
						'type' => 'options',
						'options'=>Angara_Promotions_Block_Adminhtml_Coupon_Grid::getOptionArray9(),				
						));
						
						$this->addColumn('free_product1_id', array(
						'header' => Mage::helper('promotions')->__('Free Product 1'),
						'index' => 'free_product1_id',
						'type' => 'options',
						'options'=>Angara_Promotions_Block_Adminhtml_Coupon_Grid::getOptionArray12(),				
						));
						
						$this->addColumn('free_product2_id', array(
						'header' => Mage::helper('promotions')->__('Free Product 2'),
						'index' => 'free_product2_id',
						'type' => 'options',
						'options'=>Angara_Promotions_Block_Adminhtml_Coupon_Grid::getOptionArray13(),				
						));
						
						$this->addColumn('free_product3_id', array(
						'header' => Mage::helper('promotions')->__('Free Product 3'),
						'index' => 'free_product3_id',
						'type' => 'options',
						'options'=>Angara_Promotions_Block_Adminhtml_Coupon_Grid::getOptionArray14(),				
						));
						
						$this->addColumn('free_product4_id', array(
						'header' => Mage::helper('promotions')->__('Free Product 4'),
						'index' => 'free_product4_id',
						'type' => 'options',
						'options'=>Angara_Promotions_Block_Adminhtml_Coupon_Grid::getOptionArray15(),				
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
			$this->getMassactionBlock()->addItem('remove_coupon', array(
					 'label'=> Mage::helper('promotions')->__('Remove Coupon'),
					 'url'  => $this->getUrl('*/adminhtml_coupon/massRemove'),
					 'confirm' => Mage::helper('promotions')->__('Are you sure?')
				));
			return $this;
		}
		
		static public function getOptionArray3_2()
		{
            $data_array=array(); 
			$data_array[0]='Please Select';
			
			$rulesCollection = Mage::getModel('salesrule/rule')->getCollection();
			
			$today = strtotime('now');
			foreach($rulesCollection as $rule){
				if ($rule->getIsActive()) {
					$expiryDate = $rule->getToDate();
					if($expiryDate){
						if($today < strtotime($expiryDate)){
							$data_array[$rule->getId()] = $rule->getCode();
						}
					}
				}
			}
            return($data_array);
		}
			
		static public function getOptionArray3($include_rule_id)
		{
            $data_array=array(); 
			$data_array[0]='Please Select';
			
			$options = Mage::getModel('promotions/coupon')->getCollection()->getColumnValues('rule_id');
			
			if(($key = array_search($include_rule_id, $options)) !== false) {
				unset($options[$key]);
			}
			$rulesCollection = Mage::getModel('salesrule/rule')->getCollection();
			
			if(!empty($options)){
				$rulesCollection->addFieldToFilter('rule_id', array('nin' => $options));
			}
			
			$today = strtotime('now');
			foreach($rulesCollection as $rule){
				if ($rule->getIsActive()) {
					$expiryDate = $rule->getToDate();
					if($expiryDate){
						if($today < strtotime($expiryDate)){
							$data_array[$rule->getId()] = $rule->getCode();
						}
					}
				}
			}
            return($data_array);
		}
		static public function getValueArray3($include_rule_id)
		{
            $data_array=array();
			foreach(Angara_Promotions_Block_Adminhtml_Coupon_Grid::getOptionArray3($include_rule_id) as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getOptionArray4()
		{
            $data_array=array(); 
			$data_array[0]='Please Select';
			$options = Mage::getModel('promotions/channel')->getCollection()->toOptionArray();
			foreach($options as $option){
				$data_array[$option['value']] = $option['label'];
			}
            return($data_array);
		}
		static public function getValueArray4()
		{
			$data_array = Mage::getModel('promotions/channel')->getCollection()->toOptionArray();
            return($data_array);

		}
		
		static public function getOptionArray9()
		{
            $data_array=array(); 
			$data_array[0]='Disabled';
			$data_array[1]='Enabled';
            return($data_array);
		}
		static public function getValueArray9()
		{
            $data_array=array();
			foreach(Angara_Promotions_Block_Adminhtml_Coupon_Grid::getOptionArray9() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		//	S:VA
		static public function getOptionArray10()
		{
            $data_array = array(); 
			// s: updated by pankaj
			$data_array[0] = 'Desktop / iPad / Mobile';
			$data_array[1] = 'Desktop / iPad';
			$data_array[2] = 'Desktop / Mobile';
			$data_array[3] = 'Desktop';
			$data_array[4] = 'iPad';
			$data_array[5] = 'iPad / Mobile';
			$data_array[6] = 'Mobile';
			// e: updated by pankaj
            return($data_array);
		}
		
		static public function getValueArray10()
		{
            $data_array=array();
			foreach(Angara_Promotions_Block_Adminhtml_Coupon_Grid::getOptionArray10() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);
		}
		//	E:VA
		
		static public function getOptionArray12()
		{
			$_freeProductCollection = Mage::getResourceModel('catalog/product_collection')
				->addAttributeToSelect('name')
				//->addAttributeToFilter('SKU', array('like'=>array('fr%')))
				->addAttributeToFilter(array(
					array(
						'attribute' => 'sku',
						'like' => 'fr%'),
					array(
						'attribute' => 'sku',
						'like' => 'fb%'),	
					array(
						'attribute' => 'sku',
						'like' => 'fe%'),
					array(
						'attribute' => 'sku',
						'like' => 'fp%'),						
				))
				->addAttributeToFilter('status',1);
				
			$_freeProductCollection->load();
			
			$_freeProductOptions = array(0=>"No Selection");
			foreach($_freeProductCollection as $_freeProduct){
				$_freeProductOptions[$_freeProduct->getId()] = $_freeProduct->getName();
			}
            return($_freeProductOptions);
		}
		static public function getValueArray12()
		{
            $data_array=array();
			foreach(Angara_Promotions_Block_Adminhtml_Coupon_Grid::getOptionArray12() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getOptionArray13()
		{
            return self::getOptionArray12();
		}
		
		static public function getValueArray13()
		{
            $data_array=array();
			foreach(Angara_Promotions_Block_Adminhtml_Coupon_Grid::getOptionArray13() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getOptionArray14()
		{
            return self::getOptionArray12();
		}
		
		static public function getValueArray14()
		{
            $data_array=array();
			foreach(Angara_Promotions_Block_Adminhtml_Coupon_Grid::getOptionArray14() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getOptionArray15()
		{
            return self::getOptionArray12();
		}
		
		static public function getValueArray15()
		{
            $data_array=array();
			foreach(Angara_Promotions_Block_Adminhtml_Coupon_Grid::getOptionArray15() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		// s: added by pankaj
		static public function getOptionArray16()
		{			
			$data_array = array(); 
			$availableShippingMethods = Mage::helper('function')->getDefaultShippingMethods();
			if(!empty($availableShippingMethods)){
				foreach($availableShippingMethods as $availableShipping){
					$data_array[$availableShipping['code']] = $availableShipping['title'];
				}	
			}
			return($data_array);
		}
		
		static public function getValueArray16()
		{
            $data_array = array();
			$shippingMethods = Angara_Promotions_Block_Adminhtml_Coupon_Grid::getOptionArray16();			
			foreach($shippingMethods as $k=>$v){
			   $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);
		}
		// e: added by pankaj
}