<?php
class Angara_Feedback_Helper_Data extends Mage_Core_Helper_Abstract{
	
	/*
		Check if module is active
	*/
	function canShowFeedback(){
		return Mage::getStoreConfig('feedback/settings/status');
	}
}
	 