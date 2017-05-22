<?php
class Angara_Hometabs_Block_Hometabs extends Mage_Core_Block_Template {

    public function getCategory() { 
        //$model 		= Mage::getModel('tracking/tracking');
        $request 	= 	$this->getRequest();
        $category 	= 	$request->getParam('category');
		if($category){
			return $category;
		}else{
			return false;	
		}

		//	Call model function when you want db calculations
        //return $model->getTrackingData($email, $order);
    }
}
