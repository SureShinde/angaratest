<?php
class Angara_Promotions_Model_Coupon extends Mage_Core_Model_Abstract
{
    protected function _construct(){
       $this->_init("promotions/coupon");
    }
	
	public function loadByRuleId($ruleId){
		$coupons = $this->getCollection()->addFieldToFilter('rule_id', $ruleId);
		if(count($coupons)){
			return $coupons->getFirstItem();
		}
		return false;
	}
	
	public function loadByCouponCode($couponCode){
		$rule = Mage::getModel('salesrule/coupon')->load($couponCode, 'code');
		if($rule){
			$coupons = $this->getCollection()->addFieldToFilter('rule_id', $rule->getRuleId());
			if(count($coupons)){
				return $coupons->getFirstItem();
			}
		}
		return false;
	}
	
	public function getDefaultCoupon($channel, $platform){
		$deviceApplicable = Mage::helper('promotions')->deviceApplicable($platform);
		$today = Mage::getModel('core/date')->date('Y-m-d');
		
		$coupons = $this->getCollection();
		$coupons->getSelect()->join(salesrule_coupon, 'salesrule_coupon.rule_id = main_table.rule_id', '');
		$coupons->addFieldToFilter('channel_id', $channel->getId())
			->addFieldToFilter('display_on_frontend', 1)
			->addFieldToFilter('rule_valid_for', array('in' => $deviceApplicable))
			->addFieldToFilter('expiration_date', array('gteq' => $today))
			->addFieldToFilter('is_primary', 1)
			->setOrder('priority','ASC');
		if(count($coupons)){
			return $coupons->getFirstItem();
		}
		return false;
	}
	
	public function getCoupons($channel, $platform){
		$deviceApplicable = Mage::helper('promotions')->deviceApplicable($platform);
		$today = Mage::getModel('core/date')->date('Y-m-d');
		
		$coupons = $this->getCollection();
		$coupons->getSelect()->join(salesrule_coupon, 'salesrule_coupon.rule_id = main_table.rule_id', '');
		$coupons->addFieldToFilter('channel_id', $channel->getId())
			->addFieldToFilter('display_on_frontend', 1)
			->addFieldToFilter('rule_valid_for', array('in' => $deviceApplicable))
			->addFieldToFilter('expiration_date', array('gteq' => $today))
			->addFieldToFilter('is_primary', 1)
			->setOrder('priority','ASC');
			
		if(count($coupons)){
			return $coupons;
		}
		return false;
	}
	
	/* S: Device check for coupon */
	public function validateCode($couponCode, $deviceApplicable, $today){
		$coupons = $this->getCollection();
		$coupons->getSelect()->join(salesrule_coupon, 'salesrule_coupon.rule_id = main_table.rule_id', array('rule_id','code','is_primary','expiration_date'));
		
		$coupons->addFieldToFilter('rule_valid_for', array('in' => $deviceApplicable))
			->addFieldToFilter('expiration_date', array('gteq' => $today))
			->addFieldToFilter('code', $couponCode)
			->addFieldToFilter('is_primary', 1)
			->setOrder('priority','ASC');
		
		return $coupons;
	}
	/* E: Device check for coupon */
}	 