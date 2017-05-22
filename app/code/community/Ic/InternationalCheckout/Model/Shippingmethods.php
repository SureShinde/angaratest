<?php 
class Ic_InternationalCheckout_Model_Shippingmethods extends Mage_Adminhtml_Model_System_Config_Source_Shipping_Allmethods
{
    /**
     * Return array of active carriers.
     * @param bool $isActiveOnlyFlag
     * @return array
     */
    public function toOptionArray($isActiveOnlyFlag=true)
    {
         $carrierCode = 'freedomesticshippingtoic';
		 $carrierTitle = 'Free domestic shipping to IC';
		 
		 $methods[$carrierCode] = array(
                'label'   => $carrierTitle,
                'value' => array(),
            );
			
		$methodCode = 'freedomesticshippingtoic';
		$methodTitle = 'Free';
		$methods[$carrierCode]['value'][] = array(
                    'value' => $carrierCode.'_'.$methodCode,
                    'label' => '['.$carrierCode.'] '.$methodTitle,
                );
		 
		 return array_merge(parent::toOptionArray(true), $methods);
    }
	
}