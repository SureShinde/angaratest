<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Sphinx Search Ultimate
 * @version   2.2.9
 * @revision  370
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_SearchSphinx_Adminhtml_StopwordController extends Mage_Adminhtml_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        return $this;
    }

    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('search');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Dictionary of stopwords'));
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('searchsphinx/adminhtml_stopword'));
        $this->renderLayout();
    }

    public function importAction()
    {
        $this->_initAction();

        $this->_addContent($this->getLayout()->createBlock('searchsphinx/adminhtml_stopword_import'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $result = Mage::getSingleton('searchsphinx/stopword')->import($data['file'], $data['store']);
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('searchsphinx')->__('Imported %s stopwords', $result));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);

                $this->_redirect('*/*/import');
            }
        }
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('stopword');

        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('searchsphinx')->__('Please select stopword(s)'));
        } else {
            try {
                foreach ($ids as $itemId) {
                    $model = Mage::getModel('searchsphinx/stopword')->setIsMassDelete(true)
                        ->load($itemId);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('searchsphinx')->__('Total of %d record(s) were successfully deleted', count($ids))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }
}