<?php
class Angara_EstimateShipping_AjaxController extends Mage_Core_Controller_Front_Action
{
	public function getDateAction(){
		$request = Mage::app()->getRequest();
		$extraDays = 0;
		$arriveDays = 0;	//	Change done for arrives by date
		$productId = $request->getParam('productId');
		if($request->getParam('arriveDays')){
			$extraDays = $request->getParam('extraDays');
			$arriveDays = $request->getParam('arriveDays');
		}
		echo json_encode(Mage::getModel("estimateShipping/date")->getDate($productId, $extraDays, $arriveDays));
	}
	
	public function getBeforeDateAction(){
		$request = Mage::app()->getRequest();
		$extraDays = 0;
		$arriveDays = 2;	//	Change done for arrives by date
		$productId = $request->getParam('productId');
		if($request->getParam('arriveDays')){
			$extraDays = $request->getParam('extraDays');
			$arriveDays = $request->getParam('arriveDays');
		}		
		echo Mage::getModel("estimateShipping/date")->getBeforeDate($productId, $extraDays, $arriveDays);
	}
} ?>
