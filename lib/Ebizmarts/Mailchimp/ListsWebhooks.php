<?php
/**
 * mailchimp-lib Magento Component
 *
 * @category Ebizmarts
 * @package mailchimp-lib
 * @author Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date: 5/2/16 3:51 PM
 * @file: ListsWebhooks.php
 */
class Mailchimp_ListsWebhooks extends Mailchimp_Abstract
{
    /**
     * @param string $listId               The unique id for the list.
     * @param null $id              An string that uniquely identifies this webhook.
     * @param null $url             Email address for a subscriber.
     * @param null $events          The events that can trigger the webhook and whether they are enabled.
     * @param null $sources         The possible sources of any events that can trigger the webhook and whether they are enabled.
     * @param null $_links          A list of link types and descriptions for the API schema documents.
     * @return mixed
     * @throws Mailchimp_Error
     * @throws Mailchimp_HttpError
     */
    public function add($listId,$url=null,$events=null,$sources=null)
    {
        $_params = array();
        if($url) $_params['url'] = $url;
        if($events) $_params['events'] = $events;
        if($sources) $_params['sources'] = $sources;
        
        return $this->master->call('lists/'.$listId.'/webhooks',$_params,Ebizmarts_Mailchimp::POST);
    }

    /**
     * @param $listId
     * @return mixed
     * @throws Mailchimp_Error
     * @throws Mailchimp_HttpError
     */
    public function getAll($listId)
    {
        return $this->master->call('lists/'.$listId.'/webhooks',null,Ebizmarts_Mailchimp::GET);
    }

    /**
     * @param $listId
     * @param $webhookId
     * @return mixed
     * @throws Mailchimp_Error
     * @throws Mailchimp_HttpError
     */
    public function get($listId,$webhookId)
    {
        return $this->master->call('lists/'.$listId.'/webhooks/'.$webhookId,null,Ebizmarts_Mailchimp::GET);
    }

    /**
     * @param $listId
     * @param $webhookId
     * @return mixed
     * @throws Mailchimp_Error
     * @throws Mailchimp_HttpError
     */
    public function delete($listId,$webhookId)
    {
        return $this->master->call('lists/'.$listId.'/webhooks/'.$webhookId,null,Ebizmarts_Mailchimp::DELETE);
    }
}