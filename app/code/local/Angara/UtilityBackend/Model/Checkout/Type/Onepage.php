<?php
/*
	S:VA	Model Rewrite
*/
class Angara_UtilityBackend_Model_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
	/**
     * Specify quote shipping method
     *
     * @param   string $shippingMethod
     * @return  array
     */
    public function saveShippingMethod($shippingMethod)
    {
        // Angara Modification Start
		$valid_ship_method_arr = array(
			'freeshipping_freeshipping',
			'angnonusflatrate_angnonusflatrate',
			'ang2dayflatrate_ang2dayflatrate',
			'angovernightflatrate_angovernightflatrate',
			'flatrate_flatrate'
		);			
		if (!in_array($shippingMethod, $valid_ship_method_arr)) {
			return array('error' => -1, 'message' => $this->_helper->__('Invalid shipping method.'));
		}
		// Angara Modification End
		
		if (empty($shippingMethod)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid shipping method.'));
        }
        $rate = $this->getQuote()->getShippingAddress()->getShippingRateByCode($shippingMethod);
        if (!$rate) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid shipping method.'));
        }
        $this->getQuote()->getShippingAddress()
            ->setShippingMethod($shippingMethod);

        $this->getCheckout()
            ->setStepData('shipping_method', 'complete', true)
            ->setStepData('payment', 'allow', true);

        return array();
    }
}
