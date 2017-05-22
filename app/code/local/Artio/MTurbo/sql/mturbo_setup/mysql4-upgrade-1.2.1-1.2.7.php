<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Artio
 * @package     Artio_MTurbo
 * @copyright   Copyright (c) 2010 Artio (http://www.artio.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

	$prefix = Mage::app()->getConfig()->getTablePrefix();

	$artioUrl = 'http://www.artio.net/magento-extensions/m-turbo-accelerator';

	$notification = Mage::getModel('adminNotification/inbox');
	$notf = array( 'is_read'=>0, 'date_added'=>date('Y-m-d H:i:s'), 'severity'=>4, 'url'=>$artioUrl );

	try {

		$installer = $this;

		$installer->startSetup();

		$installer->run("

			INSERT INTO `${prefix}core_config_data` (`scope`, `scope_id`, `path`, `value`) VALUES
				('default', 0, 'mturbo/dynamic_checkout_cart_link', 'div.header a.top-link-cart'),
				('default', 0, 'mturbo/downloadbatchsize', '10');

			ALTER TABLE `${prefix}mturbo` ADD UNIQUE `URL_REWRITE_ID` ( `url_rewrite_id` );
			ALTER TABLE `${prefix}core_url_rewrite` ADD INDEX `FK_CORE_URL_REWRITE_CTGR_ID_PRD_ID` (`category_id`, `product_id`);

		");

		// setup cURL multi as default download method
		if (in_array('curl', get_loaded_extensions())) {
			$installer->run("UPDATE `${prefix}core_config_data` SET value = 'curlmulti' WHERE path LIKE 'mturbo/downloadmethod'");
		}

		$installer->endSetup();

	} catch (Exception $e) {

		$notf['title'] 			= Mage::helper('mturbo')->__('M-Turbo upgrading problem');
		$notf['description']	= $e->getMessage();
		$notification->parse(array($notf));

	}
