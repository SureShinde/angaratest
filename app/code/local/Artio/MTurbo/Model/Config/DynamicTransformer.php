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
 * @category    Artio
 * @package     Artio_MTurbo
 * @copyright   Copyright (c) 2010 Artio (http://www.artio.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * The model contains function for preparing configuration data of dynamic transformers.
 *
 * @category    Artio
 * @package     Artio_MTurbo
 * @author      Artio Magento Team (jiri.chmiel@artio.cz)
 */
class Artio_MTurbo_Model_Config_DynamicTransformer extends Varien_Object
{
	
	// associative array 'name' => 'type'
	private $defaultBlocks = array(
	
		'comparesidebar' => 'catalog/product_compare_sidebar',
		'cartsidebar' 	 => 'checkout/cart_sidebar',
		'pollsidebar' 	 => 'poll/activePoll',
	
	);
	
	private $classBlocks13 = array (
	
		'comparesidebar' => 'Mage_Reports_Block_Product_Compared',
		'cartsidebar' 	 => 'Mage_Checkout_Block_Cart_Sidebar',
		'pollsidebar' 	 => 'Mage_Poll_Block_ActivePoll',
	
	);

	private $classBlocks14 = array (

		'comparesidebar' => 'Mage_Catalog_Block_Product_Compare_Sidebar',
		'cartsidebar' 	 => 'Mage_Checkout_Block_Cart_Sidebar',
		'pollsidebar' 	 => 'Mage_Poll_Block_ActivePoll',

	);
	
	private $keysDefaultBlocks = null;
	private $classBlocks	   = null;

	
	/**
	 * Method dermines whether block is default. 
	 * If block is user defined then method returns false.
	 * 
	 * @param string $identifier
	 * @return bool
	 */
	public function isDefaultBlock($identifier) {
		
		if (!isset($this->keysDefaultBlocks))
			$this->keysDefaultBlocks = array_keys($this->defaultBlocks);
			
		return in_array($identifier, $this->keysDefaultBlocks);
		
	}
	
	
	/**
	 * Method retrieves type block by identifier.
	 * Returns false, when identifier is unknowed.
	 * @param string $identifier
	 * @return bool
	 */
	public function getType($identifier) {
		
		if (isset($this->defaultBlocks[$identifier]))
			return $this->defaultBlocks[$identifier];
		else
			return false;
		
	}

	private function _getClassBlocks() {

		if (!isset($this->classBlocks)) {

			$this->classBlocks = $this->classBlocks14;
			try {
				
				$version = explode(".", Mage::getVersion());
				$this->classBlocks = (isset($version[1]) && ($version[1]=='3')) ? $this->classBlocks13 : $this->classBlocks14;

			} catch (Exception $e) { }

		}

		return $this->classBlocks;
		
	}
	
	
	/**
	 * Get the dynamic name of $block. If $block is configured
	 * as dynamic (by type or by layout name) then method returns
	 * block's name in layout else method returns false.
	 * 
	 * If $block is not specified method returns false;
	 * 
	 * @param Mage_Core_Block_Abstract $block
	 * @return bool
	 */
	public function getDynamicName($block) {

		if (!$block)
			return false;
		
		$blockTypes	= $this->_getClassBlocks();
		$configBlocks = Mage::getSingleton('mturbo/config')->getDynamicBlocksAsArray();
		
		$candidate = false;
		
		foreach ($configBlocks as $configBlock)
		{
			$configBlockWithoutLayout = preg_replace('/^[^\$]*\$/', '', $configBlock);

			if ($configBlockWithoutLayout == $block->getNameInLayout())
				return $configBlock;
			
			if (@is_a($block, $blockTypes[$configBlockWithoutLayout]))
				$candidate = $block->getNameInLayout();
		}
			
		return $candidate;
	}
	
	
	/**
	 * Method transform form data to config and fill it.
	 * @param Artio_MTurbo_Model_Config $config
	 * @param array $formData data from form
	 */
	public function extractData($config, $formData=array()) {
		
		$blocks  = array();
		
		/* foreach data */
		foreach ($formData as $key=>$value) {
			if ($this->isDefaultBlock($key))
				if ($value=='1')
					$blocks[] = $key;
		}
		
		if (isset($formData['userblocks'])) 
			$blocks = array_merge($blocks, explode(",", $formData['userblocks']));
		
		$config->setDynamicBlocks(implode(",", array_unique($blocks)));
		
		if (isset($formData['cartlink']))
			$config->setDynamicCheckoutCartLink($formData['cartlink']);
	}
	
	
	/**
     * Method transform data for administration form and retrieves as array.
     * @param Artio_MTurbo_Model_Config $config
     * @return array transformed data
     */
    public function configToForm($config) {
    	
    	/* result is empty */
    	$result = array();
    	
    	$dynamicBlocks = $config->getDynamicBlocksAsArray();
        foreach ($dynamicBlocks as $dynamic) {
    		if ($this->isDefaultBlock($dynamic)) {
    			$result[$dynamic] = '1';
    		}
    	}
    	
    	$diff	= array_diff($dynamicBlocks, $this->keysDefaultBlocks);
    	$string = implode(",", $diff);
    	$result['userblocks'] = $string;
    	$result['cartlink'] = $config->getDynamicCheckoutCartLink();
   	
    	/* return result */
    	return $result;
    	
    }
	
}