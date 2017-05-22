<?php
class Angara_Popup_Block_Popup extends Mage_Core_Block_Template
{
	protected function _prepareLayout()
    {     
        return parent::_prepareLayout();
    }    
	
	public function getCountryCode(){
		return Mage::registry('countrycode');
	}
	public function getCountryName(){
		$countryModel = Mage::getModel('directory/country')->loadByCode(Mage::registry('countrycode'));
		return $countryModel->getName();
	}
	public function getCountryCurrency(){
		$geoCountryCode = Mage::registry('countrycode');
		switch($geoCountryCode) {
            case "AU":
                $result = "Australian Dollar";
                break;
            case "GB":
                $result = "British Pound Sterling";
                break;
            case "CA":
                $result = "Canadian Dollar";
                break;
			case "PL":
                $result = "Polish Zloty";
                break;	
            default:
                $result = "US Dollar";
        }
		return $result;
	}
}