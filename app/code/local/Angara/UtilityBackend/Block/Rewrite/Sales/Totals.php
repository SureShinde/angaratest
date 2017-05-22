<?php 

/**
 * @rewrite by Asheesh
 */ 
 
class Angara_UtilityBackend_Block_Rewrite_Sales_Totals extends Mage_Adminhtml_Block_Sales_Totals
{
    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
        $this->_totals = array();
        $this->_totals['subtotal'] = new Varien_Object(array(
            'code'      => 'subtotal',
            'value'     => $this->getSource()->getSubtotal(),
            'base_value'=> $this->getSource()->getBaseSubtotal(),
            'label'     => $this->helper('sales')->__('Subtotal')
        ));

        /**
         * Add shipping
         */
        if (!$this->getSource()->getIsVirtual() && ((float) $this->getSource()->getShippingAmount() || $this->getSource()->getShippingDescription()))
        {
            $this->_totals['shipping'] = new Varien_Object(array(
                'code'      => 'shipping',
                'value'     => $this->getSource()->getShippingAmount(),
                'base_value'=> $this->getSource()->getBaseShippingAmount(),
                'label' => $this->helper('sales')->__('Shipping & Handling')
            ));
        }
		
		// Angara Modification Start
		// HPRAHI CODES START
		$cartpopupactive = 0;
		$percentage = 0;
		$discountamount = $this->getSource()->getDiscountAmount();
		$spdiscountamount = 0;
		$subtotal = $this->getSource()->getSubtotal();
		$quoteid = $this->getSource()->getQuoteId();
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		if($db)
		{			
			if($quoteid != '')
			{
				$result = $db->query("SELECT * FROM `sales_flat_quote_spdisc` where quoteid='" . $quoteid . "'");
				if($result)
				{
					$rows = $result->fetch(PDO::FETCH_ASSOC);
					if($rows) 
					{
						$cartpopupactive = 1;
						$percentage = $rows['percentage'];
						$spdiscountamount = $subtotal * $percentage/100;
						$discountamount = $discountamount + $spdiscountamount;
						$spdiscountamount = $spdiscountamount * -1;
					}
				}
			}	
		}
		// Angara Modification End
		
        /**
         * Add discount
         */
        // Angara Modification Start
		//if (((float)$this->getSource()->getDiscountAmount()) != 0) {
        if (((float)$discountamount) != 0) {
		// Angara Modification End
		    if ($this->getSource()->getDiscountDescription()) {
                $discountLabel = $this->helper('sales')->__('Discount (%s)', $this->getSource()->getDiscountDescription());
            } else {
                $discountLabel = $this->helper('sales')->__('Discount');
            }
			
			
			if($this->getSource()->getDiscountAmount() == $this->getSource()->getBaseDiscountAmount()){
				$this->_totals['discount'] = new Varien_Object(array(
					'code'      => 'discount',
					// Angara Modification Start
					'value'     => $discountamount,
					'base_value'=> $discountamount,
					// Angara Modification End
					'label'     => $discountLabel
				));
			}else{
				$this->_totals['discount'] = new Varien_Object(array(
					'code'      => 'discount',
					// Angara Modification Start
					'value'     => $this->getSource()->getDiscountAmount(),
					'base_value'=> $this->getSource()->getBaseDiscountAmount(),
					// Angara Modification End
					'label'     => $discountLabel
				));	
			}
			
        }
		
		// Angara Modification Start
		/**
         * Special discount By HPRAHI
         */
        /*if (((float)$spdiscountamount) != 0) {
            $discountLabel = "Special Discount";
            $this->_totals['discount1'] = new Varien_Object(array(
                'code'      => 'discount1',
                'value'     => $spdiscountamount,
                'base_value'=> $spdiscountamount,
                'label'     => $discountLabel
            ));
        }*/
		// HPRAHI CODES END
		// Angara Modification End
		
        $this->_totals['grand_total'] = new Varien_Object(array(
            'code'      => 'grand_total',
            'strong'    => true,
            'value'     => $this->getSource()->getGrandTotal(),
            'base_value'=> $this->getSource()->getBaseGrandTotal(),
            'label'     => $this->helper('sales')->__('Grand Total'),
            'area'      => 'footer'
        ));

        return $this;
    }
}
