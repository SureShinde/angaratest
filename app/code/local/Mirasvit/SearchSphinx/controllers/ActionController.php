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


class Mirasvit_SearchSphinx_ActionController extends Mage_Core_Controller_Front_Action
{
    public function startAction()
    {
        try {
            $this->_getEngine()->doStart();
        } catch (Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        }
    }

    public function stopAction()
    {
        try {
            $this->_getEngine()->doStop();
        } catch (Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        }

    }

    public function reindexAction()
    {
        try {
            $this->_getEngine()->doReindex();
        } catch (Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        }

    }

    public function reindexdeltaAction()
    {
        try {
            $this->_getEngine()->doReindexDelta();
        } catch (Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        }

    }

    protected function _getEngine()
    {
        return Mage::getSingleton('searchsphinx/engine_sphinx');
    }
}