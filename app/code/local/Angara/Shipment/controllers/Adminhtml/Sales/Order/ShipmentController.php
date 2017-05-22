<?php
require_once "Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php";  
class Angara_Shipment_Adminhtml_Sales_Order_ShipmentController extends Mage_Adminhtml_Sales_Order_ShipmentController{
		
	public function updateTrackAction()
    {
		//$param 		=	$this->getRequest()->getPost();
		//echo '<pre>';print_r($param);
		$id   		= 	$this->getRequest()->getPost('tracking_number');
		$is_new  	= 	$this->getRequest()->getPost('is_new');
		
		$data 		= 	array( 'is_new' => $is_new );
		$model 		= 	Mage::getModel('sales/order_shipment_track')->load($id)->addData($data);

		try {
			$model->setId($id)->save();
			$this->loadLayout();
			$response = 	'Tracking updated successfully.';	//$this->getLayout()->getBlock('shipment_tracking')->toHtml();
		} 
		catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
            );
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Cannot update Shipping and Tracking Information.'),
            );
        }
        if (is_array($response)) {
            $response = Mage::helper('core')->jsonEncode($response);
        }
		
		//$array = array('works');    
		//$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($array));
        $this->getResponse()->setBody($response);
		
    }
}
				