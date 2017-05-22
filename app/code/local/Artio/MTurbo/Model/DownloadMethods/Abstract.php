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
 * Interface for all download methods.
 *
 * @category    Artio
 * @package     Artio_MTurbo
 * @author      Artio Magento Team (jiri.chmiel@artio.cz)
 */
abstract class Artio_MTurbo_Model_DownloadMethods_Abstract
{

	protected $errorMsg = '';

	protected $batchSize = 1;

	/**
	 * Retrieves name of download method
	 */
	abstract function getName();


	/**
	 * Determines whether download method is ready for using.
	 * @return bool
	 */
	abstract function isReady();

	/**
	 * Method download choosen page by url in argument and retrieves
	 * its contents.
	 * @param array $urls
	 * @return array contents of downloaded page (url => html)
	 */
	abstract function downloadPages($urls);


	/**
	 * Determine whethe method allows parallel downloading or not.
	 *
	 * @return bool TRUE method allows parallerl downloading, FALSE method disallows
	 */
	public function allowParallelDownloading() {
		return false;
	}


	/**
	 * Set the number of url downloaded at once. There is allowed to download
	 * between 1 to 100 pages.
	 *
	 * If $size greater than 100 there will be used only 100.
	 * If $size is not a number there will be used 1.
	 *
	 * @param int $size
	 * @return Artio_MTurbo_Model_DownloadMethods_Abstract
	 */
	public function setBatchSize($size) {

		$size = (int) $size;

		$size = max(  1, $size);
		$size = min(100, $size);

		$this->batchSize = $size;

		return $this;
	}


	/**
	 * Method retrieve error message.
	 * Error message can appearance only after download any page or after call function isReady.
	 * When page is downloaded successful is returned empty string.
	 * @return string
	 */
	public function getErrorMessage() {
		return $this->errorMsg;
	}

}