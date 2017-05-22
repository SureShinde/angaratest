<?php
class Mage_Adminhtml_Block_Remaining extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {   
		$created_at		=	$row->getdata('created_at');
		$created_at     =   date('Y-m-d', strtotime($created_at));
		$status			=	$row->getdata('status');
		if($status != 'complete'){
			$types = Mage::getSingleton('payment/config')->getCcTypes();
			$last30days = date('Y-m-d', strtotime("-1 month"));
			$last28days = date('Y-m-d', strtotime("-28 days"));
			$last10days = date('Y-m-d', strtotime("-10 days"));
			$last8days = date('Y-m-d', strtotime("-8 days"));
			$lastweek = date('Y-m-d', strtotime("-1 week"));
			$last5days = date('Y-m-d', strtotime("-5 days"));
			//$last3days = date('Y-m-d', strtotime("-3 days"));
			if($created_at >= $last5days){
				return 'Under 5';
			} elseif($created_at >= $lastweek) {
				return 'Under 7';
			} elseif($created_at >= $last8days) {
				return 'Under 8';
			} elseif($created_at >= $last10days) {
				return 'Under 10';
			} elseif($created_at >= $last28days) {
				return 'Under 28';
			} elseif($created_at >= $last30days) {
				return 'Under 30';
			}
		} 
    }
}
