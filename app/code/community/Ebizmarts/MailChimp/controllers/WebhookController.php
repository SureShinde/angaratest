<?php
/**
 * MailChimp For Magento
 *
 * @category Ebizmarts_MailChimp
 * @author Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date: 5/19/16 3:55 PM
 * @file: WebhookController.php
 */
class Ebizmarts_MailChimp_WebhookController extends Mage_Core_Controller_Front_Action
{

    /**
     * Entry point for all webhook operations
     */
    public function indexAction()
    {
        
        $requestKey = $this->getRequest()->getParam('wkey');

        //Checking if "wkey" para is present on request, we cannot check for !isPost()
        //because Mailchimp pings the URL (GET request) to validate webhook
        if (!$requestKey) {
            $this->getResponse()
                ->setHeader('HTTP/1.1', '403 Forbidden')
                ->sendResponse();
            return $this;
        }
        
        $data = $this->getRequest()->getPost('data');
        $myKey = Mage::helper('mailchimp')->getWebhooksKey();

        //Validate "wkey" GET parameter
        
        if ($myKey != $requestKey) {
            if ($this->getRequest()->getPost('type')) {
                $c=Mage::getModel('mailchimp/processwebhook')->processWebhookData($this->getRequest()->getPost());
                
            }
            else
            {
                Mage::helper('mailchimp')->logError($this->__('Something went wrong with the Webhook Data'));
                Mage::helper('mailchimp')->logError($this->__($data));
            }
        }
        else
        {
            Mage::helper('mailchimp')->logError($this->__('Webhook Key invalid! Key Request: %s - My Key: %s', $requestKey, $myKey));
            Mage::helper('mailchimp')->logError($this->__('Webhook call ended'));
        }

    }

    public function runcronAction()
    {
        Mage::getModel('mailchimp/cron')->syncBatchData();
    }

    
	/**
     * S:VA		Sync subscribers FROM mailchimp list TO magento newsletter subscribers
     */
    public function syncAction()
    {
        Mage::log($this->getRequest()->getParams(),null,"mailchimp_params.log",true);
        $requestKey = $this->getRequest()->getParam('wkey');

        //Checking if "wkey" para is present on request, we cannot check for !isPost()
        if (!$requestKey) {
            $this->getResponse()
                ->setHeader('HTTP/1.1', '403 Forbidden')
                ->sendResponse();
            return $this;
        }
        
        $data = $this->getRequest()->getPost('data');
        
        if ($this->getRequest()->getPost('type')) {
            //$c    =   Mage::getModel('mailchimp/processwebhook')->processWebhookData($data);
            $this->processMailchimpSync($data);
        }else {
            Mage::log('Something went wrong with the Webhook Data'.print_r($data, true), null, 'mailchimp_sync.log', true);
        }
    }
    
    
    /*
        S:VA    Support function to sync subscribers based on data sent from mailchimp
    */  
    public function processMailchimpSync($data){

        $listId = $data['list_id'];
        /*$object = new stdClass();
        $object->requestParams = array();
        $object->requestParams['id'] = $listId;
        if (isset($data['email'])) {
            $object->requestParams['email_address'] = $data['email'];
        }
        $cacheHelper = Mage::helper('mailchimp/cache');*/
        if($data['new_email'])
        {
            $data['action'] =   'upemail'; 
        }
        else if($data['action']==''){
            $data['action'] =   'subs'; 
        }
         
        Mage::log($data['action'],null,"action.log",true);
        switch ($data['action']) {
            case 'subs':
                $this->_subscribe($data);
               // $cacheHelper->clearCache('listSubscribe', $object);
                break;
            case 'unsub':
                $this->_unsubscribe($data);
                //$cacheHelper->clearCache('listUnsubscribe', $object);
                break;
            case 'delete':
                $this->_clean($data);
                //$cacheHelper->clearCache('listUnsubscribe', $object);
                break;
            case 'upemail':
                $this->_updateEmail($data);
                //$cacheHelper->clearCache('listUpdateMember', $object);
                break;    
            case 'default':
                $this->_subscribe($data);
                //$cacheHelper->clearCache('listSubscribe', $object);
                break;
        }   
    }
    
    /*
        S:VA    Subscriber user to magento newsletter
    */
    protected function _subscribe(array $data)
    {
        try {
			Mage::log('Calling subscribe for '.print_r($data, true), null, 'mailchimp_sync.log', true);
            $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($data['email']);
            if ($subscriber->getId()) {
                $subscriber->setStatus(Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED)->save();
				Mage::log('Status set to SUBSCRIBED for '.$data['email'], null, 'mailchimp_sync.log', true);
            } else {
                $subscriber = Mage::getModel('newsletter/subscriber')->setImportMode(TRUE);
                if(isset($data['merges']['FNAME'])){
                    $subscriber->setSubscriberFirstname($data['merges']['FNAME']);
                }
                if(isset($data['merges']['LNAME'])){
                    $subscriber->setSubscriberLastname($data['merges']['LNAME']);
                }
                $subscriber->subscribe($data['email']);
				Mage::log('Subscribe done for '.$data['email'], null, 'mailchimp_sync.log', true);
            }
        } catch (Exception $e) {
            Mage::logException($e);
			Mage::log('_subscribe error '.$e->getMessage().print_r($data, true), null, 'mailchimp_sync.log', true);
        }
    }
	
	
	/**
     * Unsubscribe or delete email from Magento list
     *
     * @param array $data
     * @return void
     */
    protected function _unsubscribe(array $data)
    {
        $subscriber = $this->loadByEmail($data['email']);

        if (!$subscriber->getId()) {
            $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($data['email']);
        }

        if ($subscriber->getId()) {
            try {
                switch ($data['action']) {
                    case 'delete' :
                        //if config setting "Webhooks Delete action" is set as "Delete customer account"
                        if (Mage::getStoreConfig("mailchimp/general/webhook_delete") == 1) {
                            $subscriber->delete();
							Mage::log('Subscriber deleted '.$data['email'], null, 'mailchimp_sync.log', true);
                        } else {
                            $subscriber->setImportMode(TRUE)->unsubscribe();
							Mage::log('unsubscribe for '.$data['email'], null, 'mailchimp_sync.log', true);
                        }
                        break;
                    case 'unsub':
                        $subscriber->setImportMode(TRUE)->unsubscribe();
						Mage::log('unsubscribe done for '.$data['email'], null, 'mailchimp_sync.log', true);
                        break;
                }
            } catch (Exception $e) {
                Mage::logException($e);
				Mage::log('_unsubscribe error'.$e->getMessage().print_r($data, true), null, 'mailchimp_sync.log', true);
            }
        }
    }
    
    protected function _updateEmail(array $data)
    {

        $old = $data['old_email'];
        $new = $data['new_email'];

        $oldSubscriber = $this->loadByEmail($old);
        $newSubscriber = $this->loadByEmail($new);

        if (!$newSubscriber->getId() && $oldSubscriber->getId()) {
            $oldSubscriber->setSubscriberEmail($new)
                ->save();
        } elseif (!$newSubscriber->getId() && !$oldSubscriber->getId()) {

            //@Todo Handle merge vars on the configuration
            Mage::getModel('newsletter/subscriber')
                ->setImportMode(TRUE)
                ->setStoreId(Mage::app()->getStore()->getId())
                ->subscribe($new);
        }
    }
    /**
     * Load newsletter_subscriber by email
     *
     * @param string $email
     * @return Mage_Newsletter_Model_Subscriber
     */

    protected function _clean(array $data)
    {

      

        //Delete subscriber from Magento
        $s = $this->loadByEmail($data['email']);
        
        if ($s->getId()) {
            try {
                
                $s->delete();
                
            } catch (Exception $e) {
                
                Mage::logException($e);
            }
        }
    }

    
    public function loadByEmail($email)
    {
        return Mage::getModel('newsletter/subscriber')
            ->getCollection()
            ->addFieldToFilter('subscriber_email', $email)
            ->addFieldToFilter('store_id', Mage::app()->getStore()->getId())
            ->getFirstItem();
    }
}
