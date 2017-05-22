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
 * @version   2.2.8
 * @revision  334
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com/)
 */


/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category Mirasvit
 * @package Mirasvit_SearchIndex
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com)
 */

class Mirasvit_SearchIndex_Adminhtml_System_ActionController extends Mage_Adminhtml_Controller_Action
{

    public function reindexAction()
    {
        $index = $this->getRequest()->getParam('index');

        $result = array();
        try {
            Mage::helper('searchindex/index')->getIndexModel($index)->getIndexer()->reindexAll();

            $result['message'] = Mage::helper('searchsphinx')->__('Index has been successfully rebuilt');
        } catch(Exception $e) {
            $result['message'] = nl2br($e->getMessage());
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

}