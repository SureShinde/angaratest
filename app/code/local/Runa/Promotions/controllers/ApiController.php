<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Runa_Promotions_ApiController extends Mage_Core_Controller_Front_Action {

    public function indexAction()
    {
        $_apiType = $this->getRequest()->get('runa_remote_type');

        $_restServer = new Zend_Rest_Server();

        switch ($_apiType) {

            case 'download_order':

                $this->getRequest()->setParam('method', 'order_download');
                $_restServer->setClass('Runa_Promotions_Model_Api_Orders');

                break;

            case 'catalog' :

                $_restServer->setClass('Runa_Promotions_Model_Api_Catalog');

                break;

            case 'get_categories' :

                $this->getRequest()->setParam('method', 'get_categories');
                $_restServer->setClass('Runa_Promotions_Model_Api_Catalog');

                break;

            case 'enable_log' :

                $this->getRequest()->setParam('method', 'enable_log');
                $_restServer->setClass('Runa_Promotions_Model_Api_Debuglog');

                break;

            case 'disable_log' :

                $this->getRequest()->setParam('method', 'disable_log');
                $_restServer->setClass('Runa_Promotions_Model_Api_Debuglog');

                break;

            case 'delete_log' :

                $this->getRequest()->setParam('method', 'delete_log');
                $_restServer->setClass('Runa_Promotions_Model_Api_Debuglog');

                break;

            case 'download_log' :

                $this->getRequest()->setParam('method', 'download_log');
                $_restServer->setClass('Runa_Promotions_Model_Api_Debuglog');

                break;


            case 'generate_key':

                $_runaClient = mage::getModel('runapromotions/service_client');
                /* @var $_runaClient Runa_Promotions_Model_Service_Client */
                $_runaClient->shareAuthKeyWithRuna();

                return;
        }
        echo $_restServer->handle($this->getRequest()->getParams());
    }

}
