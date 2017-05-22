<?php
class Angara_FlashSale_Adminhtml_FlashsaleController extends Mage_Adminhtml_Controller_Action {
	
	protected function _initAction() {
		$this->loadLayout()->_setActiveMenu("flashsale/flashsale")->_addBreadcrumb(Mage::helper("adminhtml")->__("Flashsale  Manager"),Mage::helper("adminhtml")->__("Flashsale Manager"));
		return $this;
	}
	
	
	public function indexAction() {
		$this->_title($this->__("FlashSale"));
		$this->_title($this->__("Manager Flashsale"));

		$this->_initAction();
		$this->renderLayout();
	}
	
	
	public function editAction() {			    
		$this->_title($this->__("FlashSale"));
		$this->_title($this->__("Flashsale"));
		$this->_title($this->__("Edit Item"));
		
		$id = $this->getRequest()->getParam("id");
		$model = Mage::getModel("flashsale/flashsale")->load($id);
		if ($model->getId()) {
			Mage::register("flashsale_data", $model);
			$this->loadLayout();
			$this->_setActiveMenu("flashsale/flashsale");
			$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Flashsale Manager"), Mage::helper("adminhtml")->__("Flashsale Manager"));
			$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Flashsale Description"), Mage::helper("adminhtml")->__("Flashsale Description"));
			$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock("flashsale/adminhtml_flashsale_edit"))->_addLeft($this->getLayout()->createBlock("flashsale/adminhtml_flashsale_edit_tabs"));
			$this->renderLayout();
		} 
		else {
			Mage::getSingleton("adminhtml/session")->addError(Mage::helper("flashsale")->__("Item does not exist."));
			$this->_redirect("*/*/");
		}
	}


	public function newAction() {
		$this->_title($this->__("FlashSale"));
		$this->_title($this->__("Flashsale"));
		$this->_title($this->__("New Item"));
	
		$id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("flashsale/flashsale")->load($id);
	
		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}
	
		Mage::register("flashsale_data", $model);
	
		$this->loadLayout();
		$this->_setActiveMenu("flashsale/flashsale");
	
		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
	
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Flashsale Manager"), Mage::helper("adminhtml")->__("Flashsale Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Flashsale Description"), Mage::helper("adminhtml")->__("Flashsale Description"));
	
	
		$this->_addContent($this->getLayout()->createBlock("flashsale/adminhtml_flashsale_edit"))->_addLeft($this->getLayout()->createBlock("flashsale/adminhtml_flashsale_edit_tabs"));
	
		$this->renderLayout();
	}
	
	
	public function saveAction() {
		$post_data=$this->getRequest()->getPost();
		if ($post_data) {
			//prd($post_data);
			try {
				$model = Mage::getModel("flashsale/flashsale")
				->addData($post_data)
				->setId($this->getRequest()->getParam("id"))
				->save();

				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Flashsale was successfully saved"));
				Mage::getSingleton("adminhtml/session")->setFlashsaleData(false);

				if ($this->getRequest()->getParam("back")) {
					$this->_redirect("*/*/edit", array("id" => $model->getId()));
					return;
				}
				$this->_redirect("*/*/");
				return;
			} 
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
				Mage::getSingleton("adminhtml/session")->setFlashsaleData($this->getRequest()->getPost());
				$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
			return;
			}

		}
		$this->_redirect("*/*/");
	}


	public function deleteAction() {
		if( $this->getRequest()->getParam("id") > 0 ) {
			try {
				$model = Mage::getModel("flashsale/flashsale");
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


	public function massRemoveAction() {
		try {
			$ids = $this->getRequest()->getPost('flashsale_ids', array());
			foreach ($ids as $id) {
				  $model = Mage::getModel("flashsale/flashsale");
				  $model->setId($id)->delete();
			}
			Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
		}
		catch (Exception $e) {
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
		}
		$this->_redirect('*/*/');
	}
		
		
	/**
	 * Export order grid to CSV format
	 */
	public function exportCsvAction() {
		$fileName   = 'flashsale.csv';
		$grid       = $this->getLayout()->createBlock('flashsale/adminhtml_flashsale_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
	} 
	
	
	/**
	 *  Export order grid to Excel XML format
	 */
	public function exportExcelAction()	{
		$fileName   = 'flashsale.xml';
		$grid       = $this->getLayout()->createBlock('flashsale/adminhtml_flashsale_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
	}
}
