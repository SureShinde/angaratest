<?php
/**
 * Mageix_StoreLocator extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Mageix
 * @package    Mageix_ExtensionManager
 * @copyright  Copyright (c) 2011 Mageix LLC
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mageix
 * @package    Mageix_ExtensionManager
 * @author     Boris (Moshe) Gurvich <support@mageix.com>
 */
class Mageix_ExtensionManagerLicense_Block_Adminhtml_License extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'extensionmanagerlicense';
        $this->_controller = 'adminhtml_license';

        parent::__construct();

        $this->_removeButton('add');
        $this->setTemplate('extensionmanagerlicense/container.phtml');
    }
}