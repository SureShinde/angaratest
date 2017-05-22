<?php
class Angara_EstimateShipping_Model_Date extends Mage_Core_Model_Abstract
{
	public function getDate($productId, $extraDays = 0, $inputArriveDays = 0)	//	Change done for arrives by date
    {		
		$product = Mage::getModel('catalog/product')->load($productId);
		if($product && $leadTime = $product->getVendorLeadTime())
		{				
			if($product->getIsBuildYourOwn()){
				$inputArriveDays = $inputArriveDays + 2;
			}
			$todayDate = Mage::helper('function')->getCurrentServerDateTime();
			//$todayDate = Mage::helper('function')->getCurrentServerDateTimeWithMerridian();	
			//$scheduleLeadTime = Mage::helper('function')->scheduleLeadTime($todayDate);
			$dateRulesCollection = Mage::helper('function')->getDateRulesAdmin();
			
			$DefaultShippingLeadTime = Mage::helper('function')->getShippingDays('freeshipping');
			$WantItShippingLeadTime = Mage::helper('function')->getShippingDays('angovernightflatrate');
			
			$totalArriveByDays = $leadTime + $inputArriveDays + $dateRulesCollection['arriveByDays'] + $DefaultShippingLeadTime /*+ $scheduleLeadTime*/;
			$arriveDate = Mage::helper('function')->shipDateAdmin($todayDate,$totalArriveByDays);
			
			$totalWantItDays = $leadTime + $inputArriveDays + $dateRulesCollection['wantItDays'] + $WantItShippingLeadTime /*+ $scheduleLeadTime*/;
			$wantItDate = Mage::helper('function')->shipDateAdmin($todayDate,$totalWantItDays);
			$shippingMethod = 'Priority Overnight';	
			
			$leadcollection = Mage::getModel('arrivaldate/leadtimerules')->getCollection()->addFieldToFilter('lead_time',$leadTime + $inputArriveDays)->getFirstItem();
			$leadcollectiondata = $leadcollection->getData();
			if(!empty($leadcollectiondata) ) {
				if($leadcollection->getArrivalText() != 'date') {
					$arriveDate = $leadcollection->getArrivalText();							
				}
				$shippingMethod = $leadcollection->getShippingMethod();						
			}
						
			$deliveredByDays = Mage::helper('function')->shipDateAdmin($todayDate,$totalWantItDays);
			
			$deliveredByDateMDY	= date('m/d/y', strtotime($deliveredByDays));							
			$deliveredByDate = Mage::getBlockSingleton('catalog/product_deliverydate')->convertDeliveryDateToHtml($deliveredByDateMDY);	
			$dateValues = array('arriveDays' => $arriveDate,'wantitDays' => $wantItDate,'deliveredByDays' => $deliveredByDate,'shippingMethod' => $shippingMethod);
			return $dateValues;
		}			
		return 'Please call us to confirm!';
	}

	public function getCatalogDate($leadTime)
	{	
		$cacheHelper = Mage::helper('helloworld');		
		$key1 = md5('arriveDateForward');
		$key2 = md5('arriveText'.$leadTime);	
		if($arriveDateForward = $cacheHelper->getDataFromCache($key1)){		
		}		
		else
		{	
			$datecollections = Mage::getModel('arrivaldate/daterules')->getCollection();			
			$arriveDateForward = $datecollections->getFirstItem()->getDate();
			$cacheHelper->setKey($key1)->setData($arriveDateForward)->saveDataInCache();
		}				
		$arriveDays = Mage::helper('function')->getDeliveryDate($leadTime + $arriveDateForward);
		$arriveDays = date('d/m/Y', strtotime($arriveDays));
		if($arriveDaysNew = $cacheHelper->getDataFromCache($key2)){			
			if( $arriveDaysNew != 'dontcheck')
			{		
				$arriveDays = $arriveDaysNew;
			}			
		}
		else
		{	
			$leadcollection = Mage::getModel('arrivaldate/leadtimerules')->getCollection()->addFieldToFilter('lead_time',$leadTime)->getFirstItem()->getData();		
			if(!empty($leadcollection['arrival_text']) && $leadcollection['arrival_text'] != 'date')
			{					
				$arriveDaysNew = $leadcollection['arrival_text']; 
				$arriveDays = $arriveDaysNew;				
				$cacheHelper->setKey($key2)->setData($arriveDaysNew)->saveDataInCache();
			}
			else{ 
				$cacheHelper->setKey($key2)->setData('dontcheck')->saveDataInCache();
			}
		}
		return $arriveDays;
	}
} ?>
