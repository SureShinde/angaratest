<?php
/**
 * Customy
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Customy EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.customy.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@customy.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.customy.com/ for more information
 * or send an email to sales@customy.com
 *
 * @copyright  Copyright (c) 2011 Triple Dev Studio (http://www.customy.com/)
 * @license    http://www.customy.com/LICENSE-1.0.html
 */

class Customy_Tracking_Model_Tracking extends Mage_Core_Model_Abstract {

    /**
     * Loads tracking data
     *
     * @param string $email customer email
     * @param string $orderIncrementId order's increment_id
     * @return array tracks info list
     */
    public function getTrackingData($email, $orderIncrementId) 
	{
		// Get order by "order"
        $order = Mage::getModel('sales/order');
        $order->loadByAttribute('increment_id', $orderIncrementId);

        // Return empty array if order# not belongs to customer
        
		if ($email != $order['customer_email']) {
            return array();
        }

        // Get shipment of this order
        $shipments = $order->getShipmentsCollection();
		
		if (empty($shipments)) {
            return array();
        }

        // Collects trackings data from
        $result = array();
        foreach ($shipments as $shipment) { // each shipment
            $tracks = $shipment->getTracksCollection();
            if (empty($tracks)) {
                continue;
            }

            foreach ($tracks as $track) { // each track
                $track_data = $track->getData();
                if (empty($track_data)) {
                    continue;
                }
                $result[] = $track_data;
            }
        }

        return $result;
    }

}