<?php

/**
 * mailchimp-lib Magento Component
 *
 * @category Ebizmarts
 * @package mailchimp-lib
 * @author Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ebizmarts_MailChimp_Model_Api_subscribers
{
    const BATCH_LIMIT = 5000;

    public function createBatchJson($listId, $storeId, $limit)
    {
        //get subscribers
        $collection = Mage::getModel('newsletter/subscriber')->getCollection()
            ->addFieldToFilter('subscriber_status', array('eq' => 1))
            ->addFieldToFilter('store_id', array('eq' => $storeId))
            ->addFieldToFilter('mailchimp_sync_delta', array(
                array('null' => true),
                array('eq' => ''),
                array('lt' => Mage::helper('mailchimp')->getMCMinSyncDateFlag())
            ));
        //Mage::log($collection->getSelect()->__toString(),null,"mailc.log",true);
        $collection->getSelect()->limit($limit);
        $subscriberArray = array();
        $batchId = Ebizmarts_MailChimp_Model_Config::IS_SUBSCRIBER . '_' . date('Y-m-d-H-i-s');

        $counter = 0;
        foreach ($collection as $subscriber) {
            $data = $this->_buildSubscriberData($subscriber);
            $md5HashEmail = md5(strtolower($subscriber->getSubscriberEmail()));
            $subscriberJson = "";

            //enconde to JSON
            try {
                $subscriberJson = json_encode($data);

            } catch (Exception $e) {
                //json encode failed
                Mage::helper('mailchimp')->logError("Subscriber ".$subscriber->getSubscriberId()." json encode failed");
            }

            if (!empty($subscriberJson)) {
                $subscriberArray[$counter]['method'] = "PUT";
                $subscriberArray[$counter]['path'] = "/lists/" . $listId . "/members/" . $md5HashEmail;
                $subscriberArray[$counter]['operation_id'] = $batchId . '_' . $subscriber->getSubscriberId();
                $subscriberArray[$counter]['body'] = $subscriberJson;

                //update subscribers delta
                $subscriber->setData("mailchimp_sync_delta", Varien_Date::now());
                $subscriber->setData("mailchimp_sync_error", "");
                $subscriber->save();
            }
            $counter += 1;
        }
        
        return $subscriberArray;

    }

    protected function _buildSubscriberData($subscriber)
    {
        $data = array();
        $data["email_address"] = $subscriber->getSubscriberEmail();
        
        $mergeVarsall = $this->getMergeVarsAll($subscriber);
        if($mergeVarsall) {
            $data["merge_fields"] = $mergeVarsall;
        }
        $data["status_if_new"] = Mage::helper('mailchimp')->getStatus($subscriber);
        $group=Mage::helper('mailchimp')->getConfigValue(Ebizmarts_MailChimp_Model_Config::GENERAL_GROUP);
        $groupemail=Mage::helper('mailchimp')->getConfigValue(Ebizmarts_MailChimp_Model_Config::GENERAL_GROUPEMAIL);

        $interests=array();
        $subsType= $subscriber->getSubType();
        if($group && $subsType!='OrderEmail')
        {
            $interests[$group]=true;
        }
        if($groupemail && $subsType=='OrderEmail')
        {
            $interests[$groupemail]=true;
        }
       
        $data["interests"] = $interests;

        return $data;
    }

    public function addGuestSubscriber($subscriber){
        $apiKey = Mage::helper('mailchimp')->getConfigValue(Ebizmarts_MailChimp_Model_Config::GENERAL_APIKEY);
        $listId = Mage::helper('mailchimp')->getConfigValue(Ebizmarts_MailChimp_Model_Config::GENERAL_LIST);
        $status = Mage::helper('mailchimp')->getStatus();
        $api = new Ebizmarts_Mailchimp($apiKey);
        $mergeVars = $this->getMergeVars($subscriber);
        try {
            $api->lists->members->add($listId, null, $status, $subscriber->getSubscriberEmail(), $mergeVars);
        }catch(Mailchimp_Error $e){
            $this->logError($e->getFriendlyMessage());
            Mage::getSingleton('adminhtml/session')->addError($e->getFriendlyMessage());
        }catch (Exception $e){
            $this->logError($e->getMessage());
        }
    }

    public function removeSubscriber($subscriber){
        $apiKey = Mage::helper('mailchimp')->getConfigValue(Ebizmarts_MailChimp_Model_Config::GENERAL_APIKEY);
        $listId = Mage::helper('mailchimp')->getConfigValue(Ebizmarts_MailChimp_Model_Config::GENERAL_LIST);
        $api = new Ebizmarts_Mailchimp($apiKey);
        try {
            $md5HashEmail = md5(strtolower($subscriber->getSubscriberEmail()));
            $api->lists->members->update($listId, $md5HashEmail, null, 'unsubscribed');
        }
        catch(Mailchimp_Error $e){
            Mage::helper('mailchimp')->logError($e->getFriendlyMessage());
            Mage::getSingleton('adminhtml/session')->addError($e->getFriendlyMessage());
        }
        catch (Exception $e){
            Mage::helper('mailchimp')->logError($e->getMessage());
        }
    }

    protected function getMergeVars($subscriber)
    {
        $mergeVars = array();
        if($subscriber->getSubscriberFirstname()){
            $mergeVars['FNAME'] = $subscriber->getSubscriberFirstname();
        }
        if($subscriber->getSubscriberLastname()){
            $mergeVars['LNAME'] = $subscriber->getSubscriberLastname();
        }
        return (!empty($mergeVars)) ? $mergeVars : null;
    }

    

    protected function getMergeVarsAll($subscriber)
    {
        $mergeVarsall = array();
        $customer_email=$subscriber->getSubscriberEmail();
        //addition for free 2 day shipping task
        try
        {
            $sub_fname=$subscriber->getSubscriberFirstname();
            $sub_lname=$subscriber->getSubscriberLastname();
            $sub_gender=$subscriber->getGender();
            $sub_birthday=$subscriber->getBirthday();
            $sub_rel=$subscriber->getrelationshipStatus();
            $sub_wedding=$subscriber->getWeddingDate();
            $sub_zip=$subscriber->getZip();
            $mergeVarsall['FNAME']=$sub_fname?$sub_fname:"";
            $mergeVarsall['LNAME']=$sub_lname?$sub_lname:"";
            $mergeVarsall['GENDER']=$sub_gender?$sub_gender:"";
            $mergeVarsall['BIRTHDAY']=$sub_birthday?$sub_birthday:"";
            $mergeVarsall['REL']=$sub_rel?$sub_rel:"";
            $mergeVarsall['WEDDING']=$sub_wedding?$sub_wedding:"";
            $mergeVarsall['ZIP']=$sub_zip?$sub_zip:"";
        }
        catch(Exception $e)
        {
            
        }
        
       
        //end
        $orders = Mage::getModel('sales/order')->getCollection()
        ->addAttributeToFilter('customer_email', $customer_email)
        ->setOrder('created_at', Varien_Data_Collection_Db::SORT_ORDER_DESC);
        $numberOfOrders = $orders->count();
        if($numberOfOrders>0)
        {
        $newestOrder = $orders->getFirstItem();
        
        $productall=array();
        $stoneall=array();
        $sumall=0;
        foreach($orders as $all):
            $ball=$all->getIncrementId();
            $sumall+=$all->getData('base_grand_total');
            $orderall = Mage::getModel('sales/order')->loadByIncrementId($ball);
            if($orderall)
            {
                $itemsall = $orderall->getAllVisibleItems();
                foreach($itemsall as $i):
                    if(Mage::getModel('catalog/product')->load($i->getProductId())->getAttributeText('jewelry_type'))
                        $productall[]=Mage::getModel('catalog/product')->load($i->getProductId())->getAttributeText('jewelry_type');
                    if(Mage::getModel('catalog/product')->load($i->getProductId())->getAttributeText('stone1_name'))
                        $stoneall[] = Mage::getModel('catalog/product')->load($i->getProductId())->getAttributeText('stone1_name');
                 
                endforeach;

            }
            

        endforeach;

        $a= $newestOrder->getData('base_grand_total');
        
        $b=$newestOrder->getIncrementId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($b);
        $data=array();
        $product=array();
        $stone=array();
        
        if($order)
        {
            $items = $order->getAllVisibleItems();
            
            foreach($items as $i):
                if(Mage::getModel('catalog/product')->load($i->getProductId())->getAttributeText('jewelry_type'))
                    $product[]=Mage::getModel('catalog/product')->load($i->getProductId())->getAttributeText('jewelry_type');
                if(Mage::getModel('catalog/product')->load($i->getProductId())->getAttributeText('stone1_name'))
                    $stone[] = Mage::getModel('catalog/product')->load($i->getProductId())->getAttributeText('stone1_name');
                
            endforeach;
            
        }
        $name= $order->getBillingAddress()->getName();
        $data   = preg_split('/\s+/', $name);
        
        
        $mergeVarsall['NOO']=($numberOfOrders>0 ? $numberOfOrders :0);
       
        $mergeVarsall['PV']=Mage::helper('core')->currency($a, true, false);
        
        if($newestOrder->getData('created_at'))
        {
            $mergeVarsall['PD']=$newestOrder->getData('created_at');
        }
        else
        {
            $mergeVarsall['PD']="";
        }
        if($data[0])
        {
            $mergeVarsall['FNAME']=$data[0];
        }
        else
        {
            $mergeVarsall['FNAME']="";
        }
        if($data[1])
        {
            $mergeVarsall['LNAME']=$data[1];
        }
        else
        {
            $mergeVarsall['LNAME']="";
        }
        if($order->getCouponCode())
        {
            $mergeVarsall['CC']=$order->getCouponCode();
        }
        else
        {
            $mergeVarsall['CC']="";
        }
        $mergeVarsall['PVALL']=Mage::helper('core')->currency($sumall, true, false);
        if(!empty($product))
        {
            $product=(array_unique($product));

            $prefix = '';
            $products='';
            foreach ($product as $p)
            {
                $products .= $prefix .$p;
                $prefix = ', ';
            }
            $mergeVarsall['PT']=$products;
           
        }
        else
        {
            $mergeVarsall['PT']="";
        }
        if(!empty($stone))
        {
            $stone=(array_unique($stone));
            $prefix = '';
            $stones='';
            foreach ($stone as $s)
            {
                $stones .= $prefix .$s;
                $prefix = ', ';
            }
            $mergeVarsall['S']=$stones;
           
        }
        else
        {
            $mergeVarsall['S']="";
        }
        if(!empty($productall))
        {
            $productall=(array_unique($productall));
            $prefix = '';
            $productsall='';
            foreach ($productall as $p)
            {
                $productsall .= $prefix .$p;
                $prefix = ', ';
            }
            $mergeVarsall['PTALL']=$productsall;
           
        }
        else
        {
            $mergeVarsall['PTALL']="";
        }
        if(!empty($stoneall))
        {
            $stoneall=(array_unique($stoneall));
            $prefix = '';
            $stonesall='';
            foreach ($stoneall as $s)
            {
                $stonesall .= $prefix .$s;
                $prefix = ', ';
            }
            $mergeVarsall['SALL']=$stonesall;
           
        }
        else
        {
            $mergeVarsall['SALL']="";
        }
         
          
         
       } 
        
        return (!empty($mergeVarsall)) ? $mergeVarsall : null;

    }
}