<?php

class Runa_Promotions_Adminhtml_ServiceController extends Mage_Adminhtml_Controller_action {

    public function setupAction()
    {
        $_runaClient = mage::getModel('runapromotions/service_client');
        /* @var $_runaClient Runa_Promotions_Model_Service_Client */

        if ($_runaClient->shareAuthKeyWithRuna())
        {
            $settings = mage::getModel('runapromotions/config_settings');
            /* @var $settings  Runa_Promotions_Model_Config_Settings */

            echo $settings->getClientAuthKey();
        } else
        {
            echo '0';
        }
    }

}