<?php
require_once "Mage/Adminhtml/controllers/Sales/OrderController.php";  
class Angara_UtilityBackend_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController{
		
	/**
     * Save order address
    */
    public function addressSaveAction()
    {
        $addressId  = $this->getRequest()->getParam('address_id');
        $address    = Mage::getModel('sales/order_address')->load($addressId);
        $data       = $this->getRequest()->getPost();
        if ($data && $address->getId()) {
            $address->addData($data);
            try {
                $address->implodeStreetAddress()->save();
				
				//	S:VA	Store address change to order history comments
				$addressType		=	$address->getAddressType();
				$_orderId			=	$address->getParentId(); 
				$_order    			= 	Mage::getModel('sales/order')->load($_orderId);
				$addressBeforeEdit 	= 	$this->formatAddress($address->getOrigData());
				$addressAfterEdit	=	$this->formatAddress($data);
				
				if( $addressBeforeEdit !== $addressAfterEdit ){
					$history = $_order->addStatusHistoryComment('The order '.$addressType.' address has been updated. <br>From -: <br><b>'.$addressBeforeEdit.' </b><br><br>To -: <br><b>'.$addressAfterEdit.'</b>', false);
					$history->setIsCustomerNotified(false);
					$_order->save();
					
					//	Send email for address change
					$templateId			=	'1';
					$toEmail			=	'allam.ramesh@angara.com';
					$message			=	'The order '.$addressType.' address has been updated. <br>From -: <br><b>'.$addressBeforeEdit.' </b><br><br>To -: <br><b>'.$addressAfterEdit.'</b>';
					$templateVariables  = 	array('message' => $message, 'order_id' => $_order['increment_id']);
					Mage::helper('utilitybackend')->sendTransactionalEmail($templateId, $toEmail, $templateVariables );
				}
				//	E:VA
				
                $this->_getSession()->addSuccess(Mage::helper('sales')->__('The order address has been updated.'));
                $this->_redirect('*/*/view', array('order_id'=>$address->getParentId()));
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    Mage::helper('sales')->__('An error occurred while updating the order address. The address has not been changed.')
                );
            }
            $this->_redirect('*/*/address', array('address_id'=>$address->getId()));
        } else {
            $this->_redirect('*/*/');
        }
    }
	
	
	public function formatAddress($data){
		//$formattedAddress	=	$data['prefix'].' '.$data['firstname'].' '.$data['middlename'].' '.$data['lastname'].' '.$data['suffix'].'<br/>';
		$formattedAddress	=	$data['firstname'].' '.$data['lastname'].'<br/>';
		if($data['company']){
			$formattedAddress.=		$data['company'].'<br />';
		}
		if(!is_array($data['street'])){
			$formattedAddress.=		$data['street'].'<br />';
		}else{
			$formattedAddress.=		$data['street'][0].'<br />';
			if($data['street'][1]){
				$formattedAddress.=		$data['street'][1].'<br />';
			}
		}
		$region				=	$data['region'];
		if($data['region_id']){
			$region			=	Mage::getModel('directory/region')->load($data['region_id'])->getName();
		}
		$formattedAddress.=		$data['city'].', '.$region.', '.$data['postcode'].'<br/>';
		if($data['country_id']){
			$formattedAddress.=		 Mage::getModel('directory/country')->load($data['country_id'])->getName().'<br/>';
		}
		$formattedAddress.=		'T: '.$data['telephone'].'<br/>';
		if($data['fax']){
			$formattedAddress.=		'F: '.$data['fax'].'<br/>';
		}
		if($data['vat_id']){
			$formattedAddress.=		'VAT: '.$data['vat_id'];
		}
		return $formattedAddress;
	}
}
				