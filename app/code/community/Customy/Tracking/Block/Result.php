<?php
/**
 * Customy
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Customy EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.customy.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@customy.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.customy.com/ for more information
 * or send an email to sales@customy.com
 *
 * @copyright  Copyright (c) 2011 Triple Dev Studio (http://www.customy.com/)
 * @license    http://www.customy.com/LICENSE-1.0.html
 */

/**
 * Tracking form
 */
class Customy_Tracking_Block_Result extends Mage_Core_Block_Template {

    public function getTrackingData() { 
        $model = Mage::getModel('tracking/tracking');
        $request = $this->getRequest();

        $email = $request->getParam('email');
        $order = $request->getParam('order');

        return $model->getTrackingData($email, $order);
    }

    public function getErrorMessage() {
        return Mage::getStoreConfig("tracking/settings/errmess");
    }
	
	public function getTrackUrl($track_id)
	{
	  return Mage::getBaseUrl() . 'shipping/tracking/popup/track_id/' . $track_id . '/';
	}
  
	public function getCarrierUrl($carrier_code,$track_title=null)
	{
        $res = "";
        $isXtenToCarrier = false;
        switch($carrier_code){
		case 'fedex':
		  $res = Mage::getStoreConfig("tracking/links/fedexrequest");
		  break;
		case 'dhlint'://magento 1.7: DHL ()
                case 'dhl'://magento 1.7: DHL (deprecated)
		  $res =  Mage::getStoreConfig("tracking/links/dhlrequest");
		  break;
		case 'ups':
		  $res =  Mage::getStoreConfig("tracking/links/upsrequest");
		  break;
		case 'usps':
		  $res = Mage::getStoreConfig("tracking/links/uspsrequest");
		  break;
        case "tracker1"://XtenTo ext. support
            if ((string)Mage::getConfig()->getModuleConfig('Xtento_CustomTrackers')->active == 'true'  &&  Mage::getStoreConfig("customtrackers/general/enabled")  && Mage::getStoreConfig("customtrackers/tracker1/active")){
                $res = Mage::getStoreConfig("customtrackers/tracker1/url");
                $isXtenToCarrier = true;
            }
            break;
        case "tracker2"://XtenTo ext. support
            if ((string)Mage::getConfig()->getModuleConfig('Xtento_CustomTrackers')->active == 'true'  &&  Mage::getStoreConfig("customtrackers/general/enabled")  && Mage::getStoreConfig("customtrackers/tracker2/active")){
                $isXtenToCarrier = true;
                $res = Mage::getStoreConfig("customtrackers/tracker2/url");            
            }
            break;            
        case "tracker3"://XtenTo ext. support
            if ((string)Mage::getConfig()->getModuleConfig('Xtento_CustomTrackers')->active == 'true'  &&  Mage::getStoreConfig("customtrackers/general/enabled")  && Mage::getStoreConfig("customtrackers/tracker3/active") ){
                $isXtenToCarrier = true;
                $res = Mage::getStoreConfig("customtrackers/tracker3/url");
            }
            break;
        case "tracker4"://XtenTo ext. support
            if ((string)Mage::getConfig()->getModuleConfig('Xtento_CustomTrackers')->active == 'true'  &&  Mage::getStoreConfig("customtrackers/general/enabled")  && Mage::getStoreConfig("customtrackers/tracker4/active") ){            
                $isXtenToCarrier = true;
                $res = Mage::getStoreConfig("customtrackers/tracker4/url");
            }
            break;
        case "tracker5"://XtenTo ext. support
            if ((string)Mage::getConfig()->getModuleConfig('Xtento_CustomTrackers')->active == 'true'  &&  Mage::getStoreConfig("customtrackers/general/enabled")  && Mage::getStoreConfig("customtrackers/tracker5/active") ){
                $isXtenToCarrier = true;
                $res = Mage::getStoreConfig("customtrackers/tracker5/url");
            } 
            break;
        case "tracker6"://XtenTo ext. support
            if ((string)Mage::getConfig()->getModuleConfig('Xtento_CustomTrackers')->active == 'true'  &&  Mage::getStoreConfig("customtrackers/general/enabled")  && Mage::getStoreConfig("customtrackers/tracker6/active") ){
                $isXtenToCarrier = true;
                $res = Mage::getStoreConfig("customtrackers/tracker6/url");
            }
            break;
        case "tracker7"://XtenTo ext. support
            if ((string)Mage::getConfig()->getModuleConfig('Xtento_CustomTrackers')->active == 'true'  &&  Mage::getStoreConfig("customtrackers/general/enabled")  && Mage::getStoreConfig("customtrackers/tracker7/active") ){
                $res = Mage::getStoreConfig("customtrackers/tracker7/url");
                $isXtenToCarrier = true;            
            }
            break;
        case "tracker8"://XtenTo ext. support
            if ((string)Mage::getConfig()->getModuleConfig('Xtento_CustomTrackers')->active == 'true'  &&  Mage::getStoreConfig("customtrackers/general/enabled")  && Mage::getStoreConfig("customtrackers/tracker8/active") ){
                $isXtenToCarrier = true;            
                $res = Mage::getStoreConfig("customtrackers/tracker8/url");
            }
            break;
        case "tracker9"://XtenTo ext. support
            if ((string)Mage::getConfig()->getModuleConfig('Xtento_CustomTrackers')->active == 'true'   &&  Mage::getStoreConfig("customtrackers/general/enabled")  && Mage::getStoreConfig("customtrackers/tracker9/active") ){
                $isXtenToCarrier = true;            
                $res = Mage::getStoreConfig("customtrackers/tracker9/url");
            }
            break;
        case "tracker10"://XtenTo ext. support
            if ((string)Mage::getConfig()->getModuleConfig('Xtento_CustomTrackers')->active == 'true'  &&  Mage::getStoreConfig("customtrackers/general/enabled")  && Mage::getStoreConfig("customtrackers/tracker10/active") ){
                $isXtenToCarrier = true;            
                $res = Mage::getStoreConfig("customtrackers/tracker10/url");
            }
            break;

       }		
	if ( ($res == "" && ! is_null($track_title)) || //if no match odf carrier
	     ($isXtenToCarrier && ! Mage::getStoreConfig("tracking/integrations/use_xtento_urls"))//If we have to override native XtenTO URLs
	){
		if ($track_title == Mage::getStoreConfig("tracking/links/first_custom_carrier_title")){
			$res = Mage::getStoreConfig("tracking/links/first_custom_carrier_url");
		}else if ($track_title == Mage::getStoreConfig("tracking/links/second_custom_carrier_title")){
			$res =  Mage::getStoreConfig("tracking/links/second_custom_carrier_url");
		}
	}
		
	return $res;
	}//function
}
