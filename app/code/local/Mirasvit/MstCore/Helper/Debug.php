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


class Mirasvit_MstCore_Helper_Debug extends Mage_Core_Helper_Abstract
{
    protected $_filename    = null;
    protected $_enabled = null;

    public function start()
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $caller = $this->_backtrace();
        $caller['uid'] = rand(0, 1000000);

        // if ($caller['function'] == 'getContentBlock') {
        //     $x = json_encode($caller);
        //     $y = json_decode($x, TRUE);
        //     pr($y);
        //     die();
        // }

        $this->_write($caller);

        return $caller['uid'];
    }

    public function dump($uid, $data)
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $caller = $this->_backtrace();
        $caller['dumpdata'] = $data;
        $caller['puid'] = $uid;
        
        $this->_write($caller);
    }

    public function end($uid, $result)
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $caller = $this->_backtrace();
        $caller['puid'] = $uid;
        $caller['result'] = $result;

        $this->_write($caller);
    }

    public function isEnabled()
    {
        if ($this->_enabled === null) {
            if (Mage::getStoreConfig('mstcore/logger/enabled')) {
                if (Mage::getStoreConfig('mstcore/logger/developer_ip') == '*'
                    || Mage::helper('core/http')->getRemoteAddr() == Mage::getStoreConfig('mstcore/logger/developer_ip')) {
                    $this->_enabled = true;
                } elseif (Mage::helper('core/http')->getRemoteAddr() == '' && Mage::getStoreConfig('mstcore/logger/cron')) {
                    $this->_enabled = true;
                }
            }
        }

        return $this->_enabled;
    }

    protected function _backtrace()
    {
        $backtrace = debug_backtrace();
        unset($backtrace[0]);
        unset($backtrace[1]);

        $caller = $backtrace[2];
        $caller['backtrace'] = $backtrace;

        foreach ($caller['args'] as $key => $arg) {
            if (is_object($arg)) {
                $caller['args'][$key] = get_class($arg);
            }
        }

        return $caller;
    }

    protected function _getFile()
    {
        if ($this->_filename == null) {
            $uri = preg_replace('/[^a-zA-Z0-9]/', '_', Mage::app()->getRequest()->getRequestUri());
            $this->_filename = date('h-i-s').'-'.$uri;
        }

        return Mage::getBaseDir('var').DS.'log'.DS.'debug_'.$this->_filename.'.log';
    }

    protected function _write($data)
    {
        $formatter = new Zend_Log_Formatter_Simple('%message%'.PHP_EOL);
        
        $writer = new Zend_Log_Writer_Stream($this->_getFile());
        $writer->setFormatter($formatter);

        $log = new Zend_Log($writer);
        $log->log(json_encode($data), 0);
    }
}