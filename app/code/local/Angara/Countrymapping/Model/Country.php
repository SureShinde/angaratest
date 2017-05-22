<?php
    class Angara_Countrymapping_Model_Country extends Mage_Core_Model_Abstract
{
	protected $countryCode;
	protected $countryName;
	protected $countryCurrency;
	protected $countryPhone;
	
	const default_countryCode='US';
	const default_countryName='United States';
	const default_countryCurrency='USD';
	const default_countryPhone='1-888-926-4272';
	
	const XML_PATH_COUNTRY_ALLOW = 'general/country/allow';
	const XML_PATH_COUNTRY_BASE = 'general/country/default';
	
	public function _construct()
	{
		parent::_construct();
		//$this->_init('angara_countrymapping/country');
	}	
	
	protected function init_CountryParameters($code=null)
	{
		if(!$code) {
			$countryCode = Mage::getLocationFromIP();
		} else {
			$countryCode = $code;
		}		
		if(!$countryCode) {
			$countryCode=self::default_countryCode;
		}
	
		$cntry = Countrymapping::getCountryMapping();
		if(in_array($countryCode, $cntry['code'])) {
			$countryCode = $countryCode; 
		} else {
			$countryCode = self::default_countryCode; 
		}
		$this->countryCode = $countryCode;
		//save countryobj in session
		Mage::getSingleton('core/session')->setData('countryObj', serialize($this));
		//Mage::log('saved country object in session-'. $countryCode, null, Countrymapping::logFile);
		return $this;
	
	}
	
	public function getCountryParamName($code=null)
	{
		//get name from session
		if(!$code) {		
			$countryObj = unserialize(Mage::getSingleton('core/session')->getData('countryObj'));
			if(!$countryObj) {
				$countryObj = $this->init_CountryParameters();
			}
			$countryCode =  $countryObj->countryCode;
		} else {
			$countryCode =  $code;
		}
		$cntry = Countrymapping::getCountryMapping();
		if($cntry) {
			$countryName= $cntry['name'][$countryCode];
		}else {
			$countryName= self::default_countryName;	
		}				

		return $countryName;
	}

	public function getCountryParamCurrency($code=null)
	{
		//get currency from session
		if(!$code) {		
			$countryObj = unserialize(Mage::getSingleton('core/session')->getData('countryObj'));
			if(!$countryObj) {
				$countryObj = $this->init_CountryParameters();
			}	
			$countryCode =  $countryObj->countryCode;
		} else {
			$countryCode =  $code;
		}
		$cntry = Countrymapping::getCountryMapping();
		if($cntry) {
			$countryCurrency= $cntry['currency'][$countryCode];
		}else {
			$countryCurrency= self::default_countryCurrency;	
		}		

		return $countryCurrency;
	}

	public function getCountryParamPhone($code=null)
	{
		//get code from session'
		if(!$code) {	
			$countryObj = unserialize(Mage::getSingleton('core/session')->getData('countryObj'));
			if(!$countryObj) {
				$countryObj = $this->init_CountryParameters();
			}
			$countryCode =  $countryObj->countryCode;
		} else {
			$countryCode =  $code;
		}
		$cntry = Countrymapping::getCountryMapping();
		if($cntry) {
			$countryPhone= $cntry['phone'][$countryCode];
		}else {
			$countryPhone= self::default_countryPhone;	
		}
		
		return $countryPhone;
	}
	
	public function getCountryParamCode()
	{
		$countryObj = unserialize(Mage::getSingleton('core/session')->getData('countryObj'));
		if(!$countryObj) {
			$countryObj = $this->init_CountryParameters();
		}
		$countryCode = $countryObj->countryCode;	
		return $countryCode;	
	
	}
	public function getCountryParameters()
	{
		//get from session
		$countryObj = unserialize(Mage::getSingleton('core/session')->getData('countryObj'));
		if(!$countryObj) {
			$countryObj = $this->init_CountryParameters();
		}
		$cntry = Countrymapping::getCountryMapping();
		if($cntry) {
			$countryCode = $countryObj->countryCode;
			$countryName= $cntry['name'][$countryCode];
			$countryCurrency= $cntry['currency'][$countryCode];
			$countryPhone= $cntry['phone'][$countryCode];
			//if($countryName=='' OR $countryCurrency=='' OR $countryPhone=='') 
			if($countryName=='' OR $countryPhone=='' OR $countryCurrency=='') 
			{
				$countryCode= self::default_countryCode;
				$countryName= self::default_countryName;
				$countryCurrency= self::default_countryCurrency;
				$countryPhone= self::default_countryPhone;				
			}
		} else {
			$countryCode= self::default_countryCode;
			$countryName= self::default_countryName;
			$countryCurrency= self::default_countryCurrency;
			$countryPhone= self::default_countryPhone;			
		}
		$this->countryCode = $countryCode;
		$this->countryName = $countryName;
		$this->countryCurrency = $countryCurrency;
		$this->countryPhone = $countryPhone;

		return $this;
		
	}
	
	public function saveCountryCode($countryCode) 
	{
		$this->init_CountryParameters($countryCode);			

	}
	
	/*getters - to be called after- getCountryParameters or change below functions to call getCountryParamPhone, getCountryParamName separately */	
	public function getCountryName()
	{
		return $this->countryName;	
	}
	
	public function getCountryPhone()
	{
		return $this->countryPhone;	
	}
	
	public function getCountryCurrency()
	{
		return $this->countryCurrency;	
	}
	
	public function getCountryCode()
	{
		return $this->countryCode;	
	}	
	/*getters */	
}
?>