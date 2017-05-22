<?php
class Angara_Customerreview_Block_Review extends Mage_Core_Block_Template
{
	public function __construct()
    {
    
    }
    public function getCustomerOrders()
	{
		$orders = Mage::registry('customer_orders');
		return $orders;
	}
    public function getCustomerReviews()
	{
		$reviews = Mage::registry('customer_reviews');
		return $reviews;
	}	
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }
	
}
