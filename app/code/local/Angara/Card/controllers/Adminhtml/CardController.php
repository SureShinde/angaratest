<?php
class Angara_Card_Adminhtml_CardController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction()
	{
		$this->loadLayout()->_setActiveMenu("card/card")->_addBreadcrumb(Mage::helper("adminhtml")->__("Card  Manager"),Mage::helper("adminhtml")->__("Card Manager"));
		return $this;
	}
	
	public function indexAction() 
	{
		$this->_title($this->__("Card"));
		$this->_title($this->__("Manager Card"));

		$this->_initAction();
		$this->renderLayout();
	}
	
	public function editAction()
	{			    
		$this->_title($this->__("Card"));
		$this->_title($this->__("Card"));
		$this->_title($this->__("Edit Item"));
		
		$id = $this->getRequest()->getParam("id");
		$model = Mage::getModel("card/card")->load($id);
		if ($model->getId()) {
			$model['customer_name'] = Mage::helper('card')->decrypt_cont($model['customer_name']);
			$model['customer_email'] = Mage::helper('card')->decrypt_cont($model['customer_email']);
			$model['c_type'] = Mage::helper('card')->decrypt_cont($model['c_type']);
			$model['c_number'] = Mage::helper('card')->decrypt_cont($model['c_number']);
			$model['c_exp_month'] = Mage::helper('card')->decrypt_cont($model['c_exp_month']);
			$model['c_exp_year'] = Mage::helper('card')->decrypt_cont($model['c_exp_year']);
			$model['c_cvv'] = Mage::helper('card')->decrypt_cont($model['c_cvv']);
			Mage::register("card_data", $model);
			$this->loadLayout();
			$this->_setActiveMenu("card/card");
			$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Card Manager"), Mage::helper("adminhtml")->__("Card Manager"));
			$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Card Description"), Mage::helper("adminhtml")->__("Card Description"));
			$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock("card/adminhtml_card_edit"))->_addLeft($this->getLayout()->createBlock("card/adminhtml_card_edit_tabs"));
			$this->renderLayout();
		} 
		else {
			Mage::getSingleton("adminhtml/session")->addError(Mage::helper("card")->__("Item does not exist."));
			$this->_redirect("*/*/");
		}
	}

	public function newAction()
	{
		$this->_title($this->__("Card"));
		$this->_title($this->__("Card"));
		$this->_title($this->__("New Item"));

		$id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("card/card")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("card_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("card/card");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Card Manager"), Mage::helper("adminhtml")->__("Card Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Card Description"), Mage::helper("adminhtml")->__("Card Description"));


		$this->_addContent($this->getLayout()->createBlock("card/adminhtml_card_edit"))->_addLeft($this->getLayout()->createBlock("card/adminhtml_card_edit_tabs"));

		$this->renderLayout();
	}
	
	public function saveAction()
	{
		$post_data=$this->getRequest()->getPost();

		if ($post_data) {
			try {
				$post_data['customer_name'] = Mage::helper('card')->encrypt_cont(trim($post_data['customer_name']));
				$post_data['customer_email'] = Mage::helper('card')->encrypt_cont(trim($post_data['customer_email']));
				$post_data['c_type'] = Mage::helper('card')->encrypt_cont($post_data['c_type']);
				$post_data['c_number'] = Mage::helper('card')->encrypt_cont($post_data['c_number']);
				$post_data['c_exp_month'] = Mage::helper('card')->encrypt_cont($post_data['c_exp_month']);
				$post_data['c_exp_year'] = Mage::helper('card')->encrypt_cont($post_data['c_exp_year']);
				$post_data['c_cvv'] = Mage::helper('card')->encrypt_cont($post_data['c_cvv']);
								
				if(!empty($post_data['order_id'])){
					$orderId = trim($post_data['order_id']);
					$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
					
					if($order && $order->getId()){
						// Add the comment in the order
						$order->addStatusHistoryComment('A new card has been added for this order.');
						$order->save();
						
						$template = "
							Hello,<br /><br /><br />
							
							For the order number <<".$orderId.">> a new card has been added.<br /><br /><br />
							
							Regards,<br />
							Customer Support
						";
						
						$subject = 'Subject: New Card Added Order Number<<'.$orderId.'>>';
						$receiverEmail = 'allam.ramesh@angara.com';
						
						Mage::helper('card')->sendCardEmail($template, $subject, $receiverEmail);
					}
					else{
						Mage::getSingleton("adminhtml/session")->addError("Please enter a valid order number.");
						Mage::getSingleton("adminhtml/session")->setCardData($this->getRequest()->getPost());
						if ($this->getRequest()->getParam("back") && $this->getRequest()->getParam("id")) {
							$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
						}
						else{
							$this->_redirect("*/*/");
						}	
						return;
					}	
				}
				
				$model = Mage::getModel("card/card")
				->addData($post_data)
				->setId($this->getRequest()->getParam("id"))
				->save();
				
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Card was successfully saved"));
				Mage::getSingleton("adminhtml/session")->setCardData(false);

				if ($this->getRequest()->getParam("back")) {
					$this->_redirect("*/*/edit", array("id" => $model->getId()));
					return;
				}
				$this->_redirect("*/*/");
				return;
			} 
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
				Mage::getSingleton("adminhtml/session")->setCardData($this->getRequest()->getPost());
				$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
			return;
			}

		}
		$this->_redirect("*/*/");
	}

	public function deleteAction()
	{
		if( $this->getRequest()->getParam("id") > 0 ) {
			try {
				$model = Mage::getModel("card/card");
				$model->setId($this->getRequest()->getParam("id"))->delete();
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
				$this->_redirect("*/*/");
			} 
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
				$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
			}
		}
		$this->_redirect("*/*/");
	}
	
	public function massRemoveAction()
	{
		try {
			$ids = $this->getRequest()->getPost('ids', array());
			foreach ($ids as $id) {
				  $model = Mage::getModel("card/card");
				  $model->setId($id)->delete();
			}
			Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
		}
		catch (Exception $e) {
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
		}
		$this->_redirect('*/*/');
	}			
}?>
