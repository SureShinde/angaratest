<?php 

class Paypal_InContext_Helper_Data extends Mage_Core_Helper_Abstract 
{
	
	public function isMobile($regexpsConfigPath = 'design/theme/template_ua_regexp')
	{

		if (empty($_SERVER['HTTP_USER_AGENT'])) {
			return false;
		}
		
		$tablet_browser = 0;
	 
		if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
			$tablet_browser++;
		}
		 
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
			//Check for tablets on opera mini alternative headers
			$stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
			if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
			  $tablet_browser++;
			}
		}
		 
		if ($tablet_browser > 0) {
		   /* do something for tablet devices */
		   return true;
		}

		$configValueSerialized = Mage::getStoreConfig($regexpsConfigPath, Mage::app()->getStore());

		if (!$configValueSerialized) {
			return false;
		}

		$rules = @unserialize($configValueSerialized);
		if (empty($rules)) {
			return false;
		}
		foreach ($rules as $rule) {
			$regexp = '/' . trim($rule['regexp'], '/') . '/';

			if (@preg_match($regexp, $_SERVER['HTTP_USER_AGENT'])) {
				return true;
			}
		}
		return false;
	}
}