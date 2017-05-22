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
 *
 * @category    Artio
 * @package     Artio_MTurbo
 * @copyright   Copyright (c) 2010 Artio (http://www.artio.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper contains function for handling of url params.
 *
 * @category    Artio
 * @package     Artio_MTurbo
 * @author      Artio Magento Team (jiri.chmiel@artio.cz)
 */
class Artio_MTurbo_Helper_Urlparams extends Mage_Core_Helper_Abstract
{
	/* QUERY PARAMS USED IN M-TURBO EXTENSION */

	const NOCACHE       = 'nocache';                // it says "I want not cached page!"
	const DYNAMIC_BLOCK = 'mturbo_dynamic_block';   // it says "I want to use dynamic blocks!"
	const STORE         = '___store';				// magento store param

	/**
	 * Clean additional query params from url.
	 */
	public function cleanQueryParams()
	{
		if ($this->getParam(self::NOCACHE) || $this->getParam(self::DYNAMIC_BLOCK))
		{
			$this->moveAndUnset(self::NOCACHE);
			$this->moveAndUnset(self::DYNAMIC_BLOCK);
			$this->moveAndUnset(self::STORE);
		}
	}

	/**
	 * Move parameter from query string to Magento registers and then
	 * unset from query string.
	 * @param string $key
	 */
	public function moveAndUnset($key)
	{
		if (isset($_GET[$key]))
		{
			Mage::register($key, $_GET[$key], true);
			unset($_GET[$key]);
		}

		$_SERVER['REQUEST_URI'] = $this->removeRequestParam($_SERVER['REQUEST_URI'], $key);
	}

	/**
	 * Unset param from query string
	 * @param unknown_type $key
	 */
	public function unsetParam($key)
	{
		if (isset($_GET[$key]))
		{
			unset($_GET[$key]);
		}
	}

	/**
	 * Retrieve param from query string, if there is none, look into Magento registers.
	 * @param string $key
	 * @return mixed
	 */
	public function getParam($key)
	{
		return isset($_GET[$key]) ? $_GET[$key] : Mage::registry($key);
	}

	/**
	 * Determine whether no cache parameter is in current url.
	 * @return bool TRUE if yes, otherwise FALSE
	 */
	public function isNoCache()
	{
		return (bool) $this->getParam('nocache');
	}

	/**
	 * Remove request parameter from url
	 *
	 * @param string $url
	 * @param string $paramKey
	 * @return string
	 */
	public function removeRequestParam($url, $paramKey, $caseSensitive = false)
	{
		$regExpression = '/\\?[^#]*?(' . preg_quote($paramKey, '/') . '\\=[^#&]*&?)/' . ($caseSensitive ? '' : 'i');
		while (preg_match($regExpression, $url, $mathes) != 0) {
			$paramString = $mathes[1];
			if (preg_match('/&$/', $paramString) == 0) {
				$url = preg_replace('/(&|\\?)?' . preg_quote($paramString, '/') . '/', '', $url);
			} else {
				$url = str_replace($paramString, '', $url);
			}
		}
		return $url;
	}



}