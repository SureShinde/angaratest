<?php
/**
 * Sharecart Extension
 *
 * @category   Sutunam
 * @package    Sharecart
 * @author     Sututeam <dev@sutunam.com>
 */


/*
 *Sutunam Sharecart Observer class
 */

class Sutunam_Sharecart_Model_Observer 
{
    /**
     * continue process if is module sharecart
     *
     * @param Varien_Event_Observer $observer
     */
    public function continueProcess($observer)
    {
        $result = $observer->getEvent()->getResult();
        if ($result->getShouldProceed() && (bool)Mage::getStoreConfig(Sutunam_Sharecart_Model_Config::XML_PATH_SHARECART_ENABEL)) {            
            $router = Mage::app()->getRequest()->getRouteName();
            if ($router == 'sharecart') {
                //continue module sharecart ignore login
                $result->setShouldProceed(false);
            }
            return $this;
        }
    }
}