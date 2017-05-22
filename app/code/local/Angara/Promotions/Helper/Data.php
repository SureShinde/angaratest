<?php
class Angara_Promotions_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function deviceApplicable($platform){
		if($platform == 0){					//	Desktop
			$device = array('0','1','2','3');
		}
		else if($platform == 1){			//	iPad
			$device = array('0','1','4','5');
		}
		elseif($platform == 2){				//	Mobile
			$device = array('0','2','5','6');
		}
		return $device;
	}
	
	/* check the user platform and return the string */
	public function getPlatform(){
		if($this->isMobile()){
			return 2;
		}
		elseif($this->isIpad()){
			return $this->isIpad();			//	1
		}
		return 0;
	}
	
	
	/* Function to return 1 if user agent is mobile device */
	function isMobile() {
		$isMobile = (bool)preg_match('#\b(ip(hone|od)|android\b.+\bmobile|opera m(ob|in)i|windows (phone|ce)|blackberry'. '|s(ymbian|eries60|amsung)|p(alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]'. '|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $_SERVER['HTTP_USER_AGENT'] );
		return $isMobile;
	}
	
	//	Function to return 1 if user agent is iPad
	public function isIpad(){
		return (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad'); 			
	}
}