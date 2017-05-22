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
 * @copyright   Copyright (c) 2013 Artio (http://www.artio.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * The model holds codes of different methods of downloading sites.
 * It is appropriate to create as Singleton.
 *
 * @category    Artio
 * @package     Artio_MTurbo
 * @author      Artio Magento Team <info@artio.net>
 * @since		1.2.7
 */
class Artio_MTurbo_Model_DownloadQueue
{

	/**
	 * Collection with Mturbo models to download.
	 *
	 * @var Varien_Data_Collection
	 */
	protected $_collection = null;


	/**
	 * Ids of added models. This is used for
	 * quickly access.
	 *
	 * @var array
	 */
	protected $_addedIds = array();

	/**
	 * The results of downloading.
	 *
	 * @var array (url1 => message1, url2 => message2, ...)
	 */
	protected $_result = array();


	/**
	 * Add $mturboModel to collection.
	 *
	 * @param Mage_MTurbo_Model_MTurbo $mturboModel
	 * @return Artio_MTurbo_Model_DownloadQueue $this
	 */
	public function addMTurboModel($mturboModel) {

		if (!$mturboModel instanceof Artio_MTurbo_Model_MTurbo)
			return $this;

		if (is_null($this->_collection))
			$this->_collection = new Varien_Data_Collection();

		if (in_array($mturboModel->getId(), $this->_addedIds))
			return $this;

		$this->_collection->addItem($mturboModel);
		$this->_addedIds[] = $mturboModel->getId();

		return $this;
	}


	/**
	 * Return result of downloading as array.
	 *
	 * Result has follow format:
	 *
	 * array(
	 * 	url1 => error message or empty string when page was downloaded succesfuly
	 *  url2 => ...
	 *  ...
	 * )
	 *
	 * @return array
	 */
	public function getResult() {
		return $this->_result;
	}


	/**
	 * Reset the download results and empty queue.
	 *
	 * @return Artio_MTurbo_Model_DownloadQueue
	 */
	public function clearAndReset()
	{
		$this->reset();
		$this->clear();

		return $this;
	}


	/**
	 * Reset the download results.
	 *
	 * @return @return Artio_MTurbo_Model_DownloadQueue
	 */
	public function reset()
	{
		$this->_result = array();

		return $this;
	}


	/**
	 * Empty queue. All pages will be removed from queue.
	 *
	 * The result of downloading will not be reset.
	 * If you want to reset the results use method @see self::reset().
	 *
	 * @return Artio_MTurbo_Model_DownloadQueue $this
	 */
	public function clear() {

		if ($this->_collection) {
			$this->_collection->clear();
			$this->_collection = null;
		}

		$this->_addedIds = array();

		return $this;
	}


	/**
	 * Initialize download method. Download pages from queue (@see self::addMTurboModel)
	 * The result of downloading you can get by using method @see self::getResult).
	 *
	 * Queue is clear after downloading.
	 *
	 * @return Artio_MTurbo_Model_DownloadQueue $this
	 */
	public function flush() {

		// there is nothing to do for empty collection
		if (!$this->_collection || $this->_collection->getSize() == 0)
			return $this;

		// get download method code
		$config = $this->_getConfig();

		$method  = $config->getDownloadMethod();
		$minsize = $config->getMinimalPageSize();

		// instantiate download method
		$downloadMethod = Mage::getModel('mturbo/downloadMethodsFactory')->getMethod($method);

		// if download method allows paralel downloading
		// set batch size
		if ($downloadMethod->allowParallelDownloading())
			$downloadMethod->setBatchSize($config->getDownloadBatchSize());

		// pages url => path
		$pages = array();

		// get URL of requested pages
		foreach ($this->_collection->getItems() as $item) {

			$url= $item->getFileModel()->getDownloadUrlWithNoCache();

			$pages[$url] = $item;
		}

		// download pages
		$urls  = array_keys($pages);
		$htmls = $downloadMethod->downloadPages($urls);

		$this->_result = array();

		foreach ($htmls as $url => $html) {

			$item = $pages[$url];
			
			$state = $this->_checkHtml($html, $url, $minsize, $item->getStoreId());

			if (!$state) {
				$html .= "<!-- " . date('D M j H:i:s e o') . " -->";
				$state = $this->_save($html, $item);
			}

			$this->_result[$url] = $state;
		}

		$this->clear();

		return $this;
	}


	/**
	 * Save HTML ($html) to file that is specified by $item.
	 *
	 * Method gets the absolute path from Filemodel from $item.
	 * If there is not found directory of path then it will be created.
	 *
	 * Method returns empty string when all is ok, otherwise returns
	 * an error as string.
	 *
	 * @param string $html
	 * @param Mage_MTurbo_Model_MTurbo $item
	 * @return string
	 */
	protected function _save($html, $item) {

		if (!$item)
			return Mage::helper('mturbo')->__('Item is not specified');

		$path = $item->getFilemodel()->getAbsolutePath();
		$dir  = dirname($path);

		if (!file_exists($dir) && !Mage::helper('mturbo/functions')->create_dirs($dir))
			return Mage::helper('mturbo')->__("I can't create '%s'. Please, check permission to create this directory.", $dir);

		if (!file_put_contents($path, $html))
			return Mage::helper('mturbo')->__('Saving page fail.');

		return '';
	}


	/**
	 * Check HTML ($html) of downloaded pages ($url).
	 *
	 * HTML must be string lengthen than $minsize a it must not be
	 * 404 code (there is check title, if title equals 404 title then page is 404).
	 *
	 * If HTML is correct then method returns empty string, other returns
	 * error message.
	 *
	 * @param string $html HTML content
	 * @param string $url URL of downloaded page
	 * @param int $minsize minimal page size
	 * @param int $storeId used store id
	 * @return string
	 */
	protected function _checkHtml($html, $url, $minsize, $storeId) {

		if (!is_string($html))
			return Mage::helper('mturbo')->__('Page is not a string file | url: <b>%s</b>', $url);

		if (strlen($html) < $minsize)
			return Mage::helper('mturbo')->__('Page is too small | url: <b>%s</b>', $url);

		$title = (string) Mage::helper('mturbo')->getNoRouteTitle($storeId);

		if (strlen($title)>1 && strpos($html, "<title>$title") !== false)
			return Mage::helper('mturbo')->__('HTTP 404 | url: <b>%s</b>', $url);

		// empty string means correct
		return "";
	}


	/**
	 * Get standard configuration model.
	 *
	 * @return Artio_MTurbo_Model_Config
	 */
	protected function _getConfig() {
		return Mage::getSingleton('mturbo/config');
	}

}