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
 * Download method for CURL.
 *
 * @category    Artio
 * @package     Artio_MTurbo
 * @author      Artio Magento Team (jiri.chmiel@artio.cz)
 */
class Artio_MTurbo_Model_DownloadMethods_Curl extends Artio_MTurbo_Model_DownloadMethods_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Artio_MTurbo_Model_DownloadMethods_Abstract::getName()
	 */
	public function getName() {
		return Mage::helper('mturbo')->__('Using cURL (single request)');
	}


	/**
	 * (non-PHPdoc)
	 * @see Artio_MTurbo_Model_DownloadMethods_Abstract::isReady()
	 */
	public function isReady() {
		if (in_array('curl', get_loaded_extensions())) {
			$this->errorMsg = '';
			return true;
		} else {
			$this->errorMsg = Mage::helper('mturbo')->__('CURL is not installed');
			return false;
		}
	}


	/**
	 * (non-PHPdoc)
	 * @see Artio_MTurbo_Model_DownloadMethods_Abstract::allowParallelDownloading()
	 */
	public function allowParallelDownloading() {
		return false;
	}


	/**
	 * (non-PHPdoc)
	 * @see Artio_MTurbo_Model_DownloadMethods_Abstract::downloadPages()
	 */
	public function downloadPages($urls) {

		$res = array();

		foreach ($urls as $url)
			$res[$url] = $this->_downloadPage($url);

		return $res;
	}


	/**
	 * Download HTML of page specified by $url and return it.
	 *
	 * Method uses cURL extension (single request).
	 *
	 * If $url is empty or method is not ready then method returns empty string.
	 *
	 * @param string $url
	 * @return string contents of downloaded page
	 */
	protected function _downloadPage($url) {

		if (!$url || !$this->isReady())
			return '';

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$html = curl_exec($ch);
		if ($html===false)
			$this->errorMsg = curl_error($ch);

		curl_close($ch);

		return $html;
	}

}