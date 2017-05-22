<?php
class Angara_CustomerStory_Adminhtml_StoryController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
			$this->loadLayout()->_setActiveMenu("customerstory/story")->_addBreadcrumb(Mage::helper("adminhtml")->__("Shared Story Manager"),Mage::helper("adminhtml")->__("Shared Story Manager"));
			return $this;
		}
		
		public function indexAction() 
		{
			$this->_title($this->__("CustomerStory"));
			$this->_title($this->__("Manager Story"));

			$this->_initAction();
			$this->renderLayout();
		}
		
		public function editAction()
		{			    
			$this->_title($this->__("CustomerStory"));
			$this->_title($this->__("Story"));
			$this->_title($this->__("Edit Item"));
			
			$id = $this->getRequest()->getParam("id");
			$model = Mage::getModel("customerstory/story")->load($id);
			if ($model->getId()) {
				Mage::register("story_data", $model);
				$this->loadLayout();
				$this->_setActiveMenu("customerstory/story");
				$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Shared Story Manager"), Mage::helper("adminhtml")->__("Shared Story Manager"));
				$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Story Description"), Mage::helper("adminhtml")->__("Story Description"));
				$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
				$this->_addContent($this->getLayout()->createBlock("customerstory/adminhtml_story_edit"))->_addLeft($this->getLayout()->createBlock("customerstory/adminhtml_story_edit_tabs"));
				$this->renderLayout();
			} 
			else {
				Mage::getSingleton("adminhtml/session")->addError(Mage::helper("customerstory")->__("Item does not exist."));
				$this->_redirect("*/*/");
			}
		}

		public function newAction()
		{
			$this->_title($this->__("CustomerStory"));
			$this->_title($this->__("Story"));
			$this->_title($this->__("New Item"));

			$id   = $this->getRequest()->getParam("id");
			$model  = Mage::getModel("customerstory/story")->load($id);

			$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register("story_data", $model);

			$this->loadLayout();
			$this->_setActiveMenu("customerstory/story");

			$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

			$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Shared Story Manager"), Mage::helper("adminhtml")->__("Shared Story Manager"));
			$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Story Description"), Mage::helper("adminhtml")->__("Story Description"));

			$this->_addContent($this->getLayout()->createBlock("customerstory/adminhtml_story_edit"))->_addLeft($this->getLayout()->createBlock("customerstory/adminhtml_story_edit_tabs"));

			$this->renderLayout();
		}
		
		public function saveAction()
		{
			$post_data=$this->getRequest()->getPost();
			if ($post_data) {
				try {
					$model = Mage::getModel("customerstory/story")
					->addData($post_data)
					->setId($this->getRequest()->getParam("id"))
					->setData('updated_at',now())
					->save();

					Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Story was successfully saved"));
					Mage::getSingleton("adminhtml/session")->setStoryData(false);

					if ($this->getRequest()->getParam("back")) {
						$this->_redirect("*/*/edit", array("id" => $model->getId()));
						return;
					}
					$this->_redirect("*/*/");
					return;
				} 
				catch (Exception $e) {
					Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
					Mage::getSingleton("adminhtml/session")->setStoryData($this->getRequest()->getPost());
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
					$model = Mage::getModel("customerstory/story");
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
		
		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'story.csv';
			$grid       = $this->getLayout()->createBlock('customerstory/adminhtml_story_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'story.xml';
			$grid       = $this->getLayout()->createBlock('customerstory/adminhtml_story_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}?>