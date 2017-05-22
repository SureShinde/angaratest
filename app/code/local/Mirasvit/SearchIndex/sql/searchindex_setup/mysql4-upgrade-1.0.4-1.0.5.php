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


$installer = $this;

$installer->startSetup();
$installer->run("DROP TABLE IF EXISTS `{$installer->getTable('searchindex/index')}`;");
$installer->run("
CREATE TABLE `{$installer->getTable('searchindex/index')}` (
   `index_id`              int(11) NOT NULL AUTO_INCREMENT,
   `index_code`            varchar(255) NOT NULL,
   `title`                 varchar(255) NOT NULL default '',
   `position`              int(11)      NOT NULL default 0,
   `attributes_serialized` text         NULL default '',
   `properties_serialized` text         NULL default '',
   `status`                int(1)       NOT NULL default 1,
   `is_active`             int(1)       NOT NULL default 0,
   `updated_at`            datetime     NULL,
    PRIMARY KEY (`index_id`)
) ENGINE=InnoDb DEFAULT CHARSET=utf8;
");

$installer->endSetup();
