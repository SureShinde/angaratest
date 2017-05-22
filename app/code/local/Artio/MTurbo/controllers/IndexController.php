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
 * Controller
 *
 * @category    Artio
 * @package     Artio_MTurbo
 * @author      Artio Magento Team (jiri.chmiel@artio.cz)
 */
class Artio_MTurbo_IndexController extends Mage_Core_Controller_Front_Action
{


	/**
  	 * The action generates the dynamic blocks HTML as JSON.
  	 * 
  	 * Action receives param "referer" and param array "identifier".
  	 * 
  	 * Referer says which url user comes from.
  	 * 
  	 * Identifier says which blocks had to generate. Identifier can be 
  	 * either simple layout name or composite layout name with layout handle.
  	 * 
  	 * cart_sidebar - simple layout name
  	 * catalog_category_default$cart_sidebar - composite layout name with layout handle
  	 */
	public function indexAction() {
		
		// get parameters of request
		$referer = $this->getRequest()->getParam('referer');
		$ids 	 = $this->getRequest()->getParam('identifier');
		
		// prevent mturbo replacing (else really blocks would be replaced
		// by AJAX blocks again)
		Mage::register('mturbo_no_ajax', true, $graceful = true);
		
		// referer, some blocks need to get a referer, therefore
		// save it into global static variable
		Mage::register('mturbo_referer', $referer, $graceful = true);
		
		// prepare output, it will be transformed to JSON
		$output = array();

		// all has sense only if $ids is array
		if (!is_array($ids))
			return $this->_prepareResponse($output);
			
		// get the list of used layout handles and 
		// used layout names
		$layoutHandles = array();
		$layoutNames   = array();
		
		foreach ($ids as $id)
		{
			// identifier can be either simple or composite, see above
			$pid = explode('$', $id);
			
			$layoutHandles[]  = (count($pid) == 2) ? $pid[0] : 'default';
			$layoutNames[$id] = (count($pid) == 2) ? $pid[1] : $pid[0];
		}

		// load layout for all used layout handles, don't generate all blocks (this would waste time),
		// but xml must be generated, else we would not be able to found requests blocks
		$this->loadLayout($layoutHandles, $generateBlocks = false, $generateXml = true);
		
		// get loaded layout
		$layout = $this->getLayout();
		
		// generate all request blocks by theirs layout names
		foreach ($layoutNames as $id => $name)
		{
			// get node from xml, there must be generate the parent of requested block
			$node = $layout->getXpath("//block[@name='".$name."']/parent::*");

			// sometime block may not be found (bad user configuration), 
			// in these cases we skip it
			if (is_array($node) && count($node)>0)
			{
				try 
				{
					// generate and catch output the block
					$layout->generateBlocks($node[0]);
					$layout->addOutputBlock($name);
				
					$output[$id] = $layout->getOutput();
				
					$layout->removeOutputBlock($name);
				} 
				catch (Exception $e)
				{
					// there has no sense to stop working when something fails,
					// therefore only log exception and go on
					Mage::logException($e);
				}
			}
		}
		
		// update cart link text (it is special case, 
		// this is no block but only simple string
		$output['cartlink'] = $this->_updateCartLinkText();
		
		// return the response as JSON
		$this->_prepareResponse($output);
	}
	
	
	/**
	 * Set response by $output.
	 * 
	 * @param array $output
	 * @return Artio_MTurbo_IndexController
	 */
	protected function _prepareResponse($output)
	{
		$this->getResponse()->setHeader('Content-Type', 'application/json');
		$this->getResponse()->setBody(Zend_Json::encode($output));
		
		return $this;
	}
	
	
	/**
	 * Get top link cart text.
	 *  
	 * @return string
	 */
	protected function _updateCartLinkText()
	{
		$count = Mage::helper('checkout/cart')->getSummaryCount();
		
		if( $count == 1 ) {
        	$text = $this->__('My Cart (%s item)', $count);
        } elseif( $count > 0 ) {
          	$text = $this->__('My Cart (%s items)', $count);
        } else {
          	$text = $this->__('My Cart');
       	}
		return $text;
	}

}