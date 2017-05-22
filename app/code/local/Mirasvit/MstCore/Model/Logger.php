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


class Mirasvit_MstCore_Model_Logger extends Mage_Core_Model_Abstract
{
    const LOG_LEVEL_LOG         = 1;
    const LOG_LEVEL_NOTICE      = 2;
    const LOG_LEVEL_PERFORMANCE = 4;
    const LOG_LEVEL_WARNING     = 8;
    const LOG_LEVEL_EXCEPTION   = 16;
    const LOG_LEVEL_ERROR       = 32;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('mstcore/logger');
    }

    public function clean()
    {
        $date = new Zend_Date();
        $date->subDay(1);

        $collection = Mage::getModel('mstcore/logger')->getCollection()
            ->addFieldToFilter('created_at', array('lt' => $date->toString('Y-MM-dd H:mm:s')));

        foreach($collection as $entry) {
            $entry->delete();
        }
        return $this;
    }
}