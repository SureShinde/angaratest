<?php

class Runa_Promotions_Model_Events_layout_Select extends Varien_Object {

    public function selectFrontendLayout($event) {
        $_updates = $event->getUpdates();

        $ver = Mage::getVersion();
        $version = str_replace('.', '', $ver);

        //loading magento version spesific layout file
        if ($version >= 1400) {
            //if version is >1.4.X remove 1.3.X layout
            unset($_updates->runapromotions_3_X[0]);
        }else{
            //load 1.3.X layout
            unset($_updates->runapromotions[0]);
        }
        return;
    }
    
    public function selectAdminLayout($event) {
        $_updates = $event->getUpdates();

        $ver = Mage::getVersion();
        $version = str_replace('.', '', $ver);

        //loading magento version spesific layout file
        if ($version >= 1400) {
            //if version is >1.4.X remove 1.3.X layout
            unset($_updates->runapromotions_admin_3_X[0]);
        }else{
            //load 1.3.X layout
            unset($_updates->runapromotions_admin[0]);
        }
        return;
    }

}