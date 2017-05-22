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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Fedex shipping implementation
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Customy_Tracking_Model_Rewrite_Mage_Usa_Model_Shipping_Carrier_Fedex  
        extends Mage_Usa_Model_Shipping_Carrier_Fedex
            implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * Parse tracking response
     *
     * @param array $trackingValue
     * @param stdClass $response
     */
    protected function _parseTrackingResponse($trackingValue, $response)
    {
        if (is_object($response)) {
            if ($response->HighestSeverity == 'FAILURE' || $response->HighestSeverity == 'ERROR') {
                $errorTitle = (string)$response->Notifications->Message;
            } elseif (isset($response->TrackDetails)) {
                $trackInfo = $response->TrackDetails;
                $resultArray['status'] = (string)$trackInfo->StatusDescription;
                $resultArray['service'] = (string)$trackInfo->ServiceInfo;
                $timestamp = isset($trackInfo->EstimatedDeliveryTimestamp) ?
                    $trackInfo->EstimatedDeliveryTimestamp : $trackInfo->ActualDeliveryTimestamp;
                $timestamp = strtotime((string)$timestamp);
                if ($timestamp) {
                    $resultArray['deliverydate'] = date('Y-m-d', $timestamp);
                    $resultArray['deliverytime'] = date('H:i:s', $timestamp);
                }

                $deliveryLocation = isset($trackInfo->EstimatedDeliveryAddress) ?
                    $trackInfo->EstimatedDeliveryAddress : $trackInfo->ActualDeliveryAddress;
                $deliveryLocationArray = array();
                if (isset($deliveryLocation->City)) {
                    $deliveryLocationArray[] = (string)$deliveryLocation->City;
                }
                if (isset($deliveryLocation->StateOrProvinceCode)) {
                    $deliveryLocationArray[] = (string)$deliveryLocation->StateOrProvinceCode;
                }
                if (isset($deliveryLocation->CountryCode)) {
                    $deliveryLocationArray[] = (string)$deliveryLocation->CountryCode;
                }
                if ($deliveryLocationArray) {
                    $resultArray['deliverylocation'] = implode(', ', $deliveryLocationArray);
                }

                $resultArray['signedby'] = (string)$trackInfo->DeliverySignatureName;
                $resultArray['shippeddate'] = date('Y-m-d', (int)$trackInfo->ShipTimestamp);
                if (isset($trackInfo->PackageWeight) && isset($trackInfo->Units)) {
                    $weight = (string)$trackInfo->PackageWeight;
                    $unit = (string)$trackInfo->Units;
                    $resultArray['weight'] = "{$weight} {$unit}";
                }

                $packageProgress = array();
                if (isset($trackInfo->Events)) {
                    $events = $trackInfo->Events;
                    if (isset($events->Address)) {
                        $events = array($events);
                    }
                    foreach ($events as $event) {
                        $tempArray = array();
                        $tempArray['activity'] = (string)$event->EventDescription;
                        $timestamp = strtotime((string)$event->Timestamp);
                        if ($timestamp) {
                            $tempArray['deliverydate'] = date('Y-m-d', $timestamp);
                            $tempArray['deliverytime'] = date('H:i:s', $timestamp);
                        }
                        if (isset($event->Address)) {
                            $addressArray = array();
                            $address = $event->Address;
                            if (isset($address->City)) {
                                $addressArray[] = (string)$address->City;
                            }
                            if (isset($address->StateOrProvinceCode)) {
                                $addressArray[] = (string)$address->StateOrProvinceCode;
                            }
                            if (isset($address->CountryCode)) {
                                $addressArray[] = (string)$address->CountryCode;
                            }
                            if ($addressArray) {
                                $tempArray['deliverylocation'] = implode(', ', $addressArray);
                            }
                        }
                        $packageProgress[] = $tempArray;
                    }
                }

                $resultArray['progressdetail'] = $packageProgress;
            }
        }

        if (!$this->_result) {
            $this->_result = Mage::getModel('shipping/tracking_result');
        }

        if (isset($resultArray)) {
            $tracking = Mage::getModel('shipping/tracking_result_status');
            $tracking->setCarrier('fedex');
            $tracking->setCarrierTitle($this->getConfigData('title'));
            $tracking->setTracking($trackingValue);
            $tracking->addData($resultArray);
            $this->_result->append($tracking);
        } else {
           $error = Mage::getModel('shipping/tracking_result_error');
           $error->setCarrier('fedex');
           $error->setCarrierTitle($this->getConfigData('title'));
           $error->setTracking($trackingValue);
           //Customy bugfix: check $errorTitle existanse
           $error->setErrorMessage(isset($errorTitle) ? $errorTitle : Mage::helper('usa')->__('Unable to retrieve tracking'));
           $this->_result->append($error);
        }
    }

}
