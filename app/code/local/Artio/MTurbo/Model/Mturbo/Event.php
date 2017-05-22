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
 * MTurbo event model.
 *
 *
 * @category    Artio
 * @package     Artio_MTurbo
 * @author      Artio Magento Team (jiri.chmiel@artio.cz)
 */
class Artio_MTurbo_Model_MTurbo_Event extends Mage_Core_Model_Abstract {

	const TYPE_CATEGORY_ID 	= '1';
	const TYPE_PRODUCT_ID  	= '2';
	const TYPE_REWRITE_ID  	= '3';
	const TYPE_REQUEST	   	= '4';
	const TYPE_CMS_ID	   	= '5';

	/**
	 * Static queue of requests to recache pages.
	 *
	 * The request to recache pages is Varien_Object
	 * with 'type' and 'ids'. 'type' is type of pages
	 * to recahce (see constants TYPE_*) ids is the
	 * identifiers of the pages.
	 *
	 * @see Artio_MTurbo_Model_MTurbo_Event::addItem
	 * @var array
	 */
	protected static $queue = array();

	/**
	 * Sync flag. If it set true then method
	 * "flush" calls synchronize on Mturbo/Mturbo model.
	 *
	 * It is required if someone add category or cms page and
	 * there is set "add new category" or "add new page" to
	 * select.
	 *
	 * @var bool
	 */
	protected static $synchronize = false;


	/**
	 * Add item into queue.
	 *
	 * Ex.: recache product with id=12 and id=13
	 *
	 * <pre>
	 * $queue = Mage::getModel('mturbo/mturbo_event');
	 * $queue->addItem(Artio_MTurbo_Model_MTurbo_Event::TYPE_PRODUCT_ID, array(12,13));
	 * </pre>
	 *
	 * You can execute recaching by call method
	 * @see Artio_MTurbo_Model_MTurbo_Event::flush()
	 *
	 * @param int $type constant TYPE_*
	 * @param string|array $ids the ids of item or items
	 * @return Artio_MTurbo_Model_MTurbo_Event
	 */
	public function addItem($type, $ids) {

		self::$queue[] = new Varien_Object(array(
			'type' => $type,
			'ids'  => $ids
		));

		return $this;
	}


	/**
	 * Set synchronize flag.
	 *
	 * If flag is true then method "flush" will
	 * call synchronize before recaching.
	 *
	 * @param bool $flag
	 * @return Artio_MTurbo_Model_MTurbo_Event
	 */
	public function setSynchronizeFlag($flag) {

		self::$synchronize = $flag;

		return $this;
	}


	/**
	 * Flush current queue.
	 *
	 * @see Artio_MTurbo_Model_MTurbo_Event::addItem
	 * @return Artio_MTurbo_Model_MTurbo_Event
	 */
	public function flush() {

		if (self::$synchronize)
			$this->_getMTurboModel()->synchronize();

		foreach (self::$queue as $action)
			$this->flushAction($action);

		return $this;
	}


	/**
	 * Flush one action specified by $action.
	 *
	 * $action must be Varien_Object and must have
	 * member 'type' and 'ids'. If no, nothing will be done.
	 *
	 * Ex.: recache product with id=12 and id=13
	 *
	 * <pre>
	 *   $queue = Mage::getModel('mturbo/mturbo_event');
	 *   $queue->flushAction(new Varien_Object(array(
	 *   	'type' 	=> Artio_MTurbo_Model_MTurbo_Event::TYPE_PRODUCT_ID,
	 *   	'ids'	=> array(12,13)))
	 *   );
	 * </pre>
	 *
	 * @param Varien_Object $action
	 * @return Artio_MTurbo_Model_MTurbo_Event
	 */
	public function flushAction($action)
	{
		$mturbo = $this->_getMTurboModel();

	    $collection = null;

		switch ($action->getData('type')) {

			case self::TYPE_CATEGORY_ID:
				$collection = $mturbo->getCollectionByCategoryIds($action->getData('ids'));
				break;
			case self::TYPE_PRODUCT_ID:
				$collection = $mturbo->getCollectionByProductIds($action->getData('ids'));
				break;
			case self::TYPE_REQUEST:
				break;
			case self::TYPE_CMS_ID:
				$collection = $mturbo->getCollectionByCmsIds($action->getData('ids'));
				break;
			case self::TYPE_REWRITE_ID:
				break;

		}

		if (isset($collection)) {

			$downloadQueue = Mage::getSingleton('mturbo/downloadQueue');
			$downloadQueue->clearAndReset();

			foreach ($collection->getItems() as $item)
				if (!$item->isBlocked())
					$downloadQueue->addMTurboModel($item);

			$downloadQueue->flush();
		}

	    return $this;
	}


	/**
	 * @return Artio_MTurbo_Model_MTurbo
	 */
	protected function _getMTurboModel()
	{
		return Mage::getModel('mturbo/mturbo');
	}

}
