<?php

class Angara_Ship_Adminhtml_ShipController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("ship/ship")->_addBreadcrumb(Mage::helper("adminhtml")->__("Ship  Manager"),Mage::helper("adminhtml")->__("Ship Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Ship"));
			    $this->_title($this->__("Manager Ship"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Ship"));
				$this->_title($this->__("Ship"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("ship/ship")->load($id);
				if ($model->getId()) {
					Mage::register("ship_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("ship/ship");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Ship Manager"), Mage::helper("adminhtml")->__("Ship Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Ship Description"), Mage::helper("adminhtml")->__("Ship Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("ship/adminhtml_ship_edit"))->_addLeft($this->getLayout()->createBlock("ship/adminhtml_ship_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("ship")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Ship"));
		$this->_title($this->__("Ship"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("ship/ship")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("ship_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("ship/ship");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Ship Manager"), Mage::helper("adminhtml")->__("Ship Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Ship Description"), Mage::helper("adminhtml")->__("Ship Description"));


		$this->_addContent($this->getLayout()->createBlock("ship/adminhtml_ship_edit"))->_addLeft($this->getLayout()->createBlock("ship/adminhtml_ship_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("ship/ship")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Ship was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setShipData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setShipData($this->getRequest()->getPost());
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
						$model = Mage::getModel("ship/ship");
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
				$ids = $this->getRequest()->getPost('ship_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("ship/ship");
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
		public function exportCsvAction()
		{
			$fileName   = 'ship.csv';
			$grid       = $this->getLayout()->createBlock('ship/adminhtml_ship_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'ship.xml';
			$grid       = $this->getLayout()->createBlock('ship/adminhtml_ship_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
