<?php
/**
 * Customy
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Customy EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.customy.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@customy.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.customy.com/ for more information
 * or send an email to sales@customy.com
 *
 * @copyright  Copyright (c) 2011 Triple Dev Studio (http://www.customy.com/)
 * @license    http://www.customy.com/LICENSE-1.0.html
 */

class Customy_Tracking_Model_Observer {
    
    public function beforeLoadLayout($observer) 
	{
		if (! Mage::getStoreConfig('tracking/settings/enabled')) {
            return;
        }
	
		if (Mage::getStoreConfig('tracking/settings/toplinks_enable')) {
            $observer->getEvent()->getLayout()->getUpdate()
                ->addHandle('tracking_toplinks_menu');            
        }
        
        if (Mage::getStoreConfig('tracking/settings/footerlinks_enable')) {
            $observer->getEvent()->getLayout()->getUpdate()
                ->addHandle('tracking_footerlinks_menu');                       
        }
    }
    
}
