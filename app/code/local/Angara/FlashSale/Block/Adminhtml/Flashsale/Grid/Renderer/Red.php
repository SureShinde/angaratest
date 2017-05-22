<?php
class Angara_FlashSale_Block_Adminhtml_Flashsale_Grid_Renderer_Red extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
	 
	public function render(Varien_Object $row)
	{
		//prd($row->getdata());
		$from_date		=	$row->getdata('from_date');
		$to_date		=	$row->getdata('to_date');
		$is_active		=	$row->getdata('is_active');
		$currentTime	=	Mage::helper('flashsale')->getCurrentServerDateTime();
		if($is_active==1){
			$value =  $row->getData($this->getColumn()->getIndex());
			return '<b style="color:red;">'.$value.' Disabled</b>';								
		}elseif($currentTime < $from_date){
			$value =  $row->getData($this->getColumn()->getIndex());
			return '<b style="color:#26AEE3;">'.$value.' Not Started</b>';								
		}elseif($to_date < $currentTime){
			$value =  $row->getData($this->getColumn()->getIndex());
			return '<b style="color:#DDD;">'.$value.' Has Expired</b>';								
		}else{
			$value =  $row->getData($this->getColumn()->getIndex());
			return '<b style="color:#76C25C;">'.$value.' Running</b>';		
		}
	}
}
?>