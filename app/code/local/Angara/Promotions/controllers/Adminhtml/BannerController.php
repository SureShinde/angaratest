<?php

class Angara_Promotions_Adminhtml_BannerController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("promotions/banner")->_addBreadcrumb(Mage::helper("adminhtml")->__("Banner  Manager"),Mage::helper("adminhtml")->__("Banner Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Promotions"));
			    $this->_title($this->__("Manager Banner"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Promotions"));
				$this->_title($this->__("Banner"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("promotions/banner")->load($id);
				if ($model->getId()) {
					Mage::register("banner_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("promotions/banner");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Banner Manager"), Mage::helper("adminhtml")->__("Banner Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Banner Description"), Mage::helper("adminhtml")->__("Banner Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("promotions/adminhtml_banner_edit"))->_addLeft($this->getLayout()->createBlock("promotions/adminhtml_banner_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("promotions")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Promotions"));
		$this->_title($this->__("Banner"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("promotions/banner")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("banner_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("promotions/banner");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Banner Manager"), Mage::helper("adminhtml")->__("Banner Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Banner Description"), Mage::helper("adminhtml")->__("Banner Description"));


		$this->_addContent($this->getLayout()->createBlock("promotions/adminhtml_banner_edit"))->_addLeft($this->getLayout()->createBlock("promotions/adminhtml_banner_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {						
				 		//save image
						try{
				
							if((bool)$post_data['image_path']['delete']==1) {
							
										$post_data['image_path']='';
							
							}
							else {
							
								unset($post_data['image_path']);
							
								if (isset($_FILES)){
							
									if ($_FILES['image_path']['name']) {
							
										if($this->getRequest()->getParam("id")){
											$model = Mage::getModel("promotions/banner")->load($this->getRequest()->getParam("id"));
											if($model->getData('image_path')){
													$io = new Varien_Io_File();
													$io->rm(Mage::getBaseDir('media').DS.implode(DS,explode('/',$model->getData('image_path'))));	
											}
										}
													$path = Mage::getBaseDir('media') . DS . 'promotions' . DS .'banner'.DS;
													$uploader = new Varien_File_Uploader('image_path');
													$uploader->setAllowedExtensions(array('jpg','png','gif'));
													$uploader->setAllowRenameFiles(false);
													$uploader->setFilesDispersion(false);
													$destFile = $path.$_FILES['image_path']['name'];
													$filename = $uploader->getNewFileName($destFile);
													$uploader->save($path, $filename);
							
													$post_data['image_path']='promotions/banner/'.$filename;
									}
								}
							}
				
						} catch (Exception $e) {
								Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
								$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
								return;
						}
						//save image
						if(empty($post_data['created_at'])){
							$post_data['created_at'] = Mage::getSingleton('core/date')->gmtDate();
							$post_data['updated_at'] = $post_data['created_at'];
						}
						else{
							$post_data['updated_at'] = Mage::getSingleton('core/date')->gmtDate();
						}


						$model = Mage::getModel("promotions/banner")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Banner was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setBannerData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setBannerData($this->getRequest()->getPost());
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
						$model = Mage::getModel("promotions/banner");
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
                      $model = Mage::getModel("promotions/banner");
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
			$fileName   = 'banner.csv';
			$grid       = $this->getLayout()->createBlock('promotions/adminhtml_banner_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'banner.xml';
			$grid       = $this->getLayout()->createBlock('promotions/adminhtml_banner_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
