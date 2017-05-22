<?php
/*
	S:VA	Model Rewrite
*/
class Angara_UtilityBackend_Model_Sales_Order extends Mage_Sales_Model_Order
{
    
    /*
     * Add a comment to order
     * Different or default status may be specified
     *
     * @param string $comment
     * @param string $status
     * @return Mage_Sales_Order_Status_History
     */
    public function addStatusHistoryComment($comment, $status = false)
    {
        if (false === $status) {
            $status = $this->getStatus();
        } elseif (true === $status) {
            $status = $this->getConfig()->getStateDefaultStatus($this->getState());
        } else {
            $this->setStatus($status);
        }
		// Angara Modification Start
		$admin_data = Mage::getSingleton('admin/session')->getUser();
		if($admin_data){	
			$revised_comment = $comment.'<br>Updated by <b>'.$admin_data->getFirstname().' '.$admin_data->getLastname().'</b>';
		}else{
			$revised_comment = $comment;
		}
		// Angara Modification End
        $history = Mage::getModel('sales/order_status_history')
            ->setStatus($status)
            ->setComment($revised_comment)
            ->setEntityName($this->_historyEntityName);
        $this->addStatusHistory($history);
        return $history;
    }

  
    public function getCustomerName()
    {
        if ($this->getCustomerFirstname()) {
            $customerName = Mage::helper('customer')->getFullCustomerName($this);
        } else {
            $customerName = Mage::helper('sales')->__('Guest');
        }
        // Angara Modification Start
		$customerName = ucwords($customerName);
		// Angara Modification End
        return $customerName;
    }

}
