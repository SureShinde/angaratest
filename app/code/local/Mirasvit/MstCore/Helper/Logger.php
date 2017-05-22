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


class Mirasvit_MstCore_Helper_Logger extends Mage_Core_Helper_Abstract
{
    public function log($object, $message, $content = null, $level = null, $trace = false)
    {
        if(!Mage::getStoreConfig('mstcore/logger/enabled')) {
            return $this;
        }

        $logger = $this->_getLoggerObject();
        $logger->setData(array());

        $className = is_string($object) ? $object : get_class($object);
        if(preg_match("/Mirasvit_([a-z]+)+/i", $className, $matches)) {
            if (isset($matches[1])) {
                $logger->setModule($matches[1]);
            }
        }

        $logger->setMessage($message)
            ->setContent($content)
            ->setClass($className)
            ->setLevel($level);

        if ($level >= Mirasvit_MstCore_Model_Logger::LOG_LEVEL_WARNING || $trace) {
            $logger->setTrace(Varien_Debug::backtrace(true, false));
        }

        $logger->save();

        return $this;
    }

    public function logException($object, $message, $content = null, $trace = false)
    {
        $this->log($object, $message, $content, Mirasvit_MstCore_Model_Logger::LOG_LEVEL_EXCEPTION);
    }

    public function logPerformance($object, $message, $time = null, $trace = false)
    {
        $this->log($object, $message, $time, Mirasvit_MstCore_Model_Logger::LOG_LEVEL_PERFORMANCE);
    }

    protected function _getLoggerObject()
    {
        return Mage::getSingleton('mstcore/logger');
    }
}