<?php
/**
 * MailChimp For Magento
 *
 * @category Ebizmarts_MailChimp
 * @author Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date: 4/29/16 3:55 PM
 * @file: Account.php
 */
class Ebizmarts_MailChimp_Model_System_Config_Source_Group
{


    protected $_groups = null;

    public function __construct()
    {
        if (is_null($this->_groups)) 
        {
            $apiKey = Mage::helper('mailchimp')->getConfigValue(Ebizmarts_MailChimp_Model_Config::GENERAL_APIKEY);
                if ($apiKey) 
                {
                    $api = new Ebizmarts_Mailchimp($apiKey);
                    $listId = Mage::helper('mailchimp')->getConfigValue(Ebizmarts_MailChimp_Model_Config::GENERAL_LIST, $storeId);
                    if($listId)
                    {
                        $id_array = $api->lists->interestCategory->getAll($listId);
                       
                        $id=$id_array['categories'][0]['id'];

                        //$group_info_array = $mailchimpApi->lists->interestCategory->interests->getAll($listid,$id);
                       
                        if($id)
                        {
                             
                            $this->_groups=$api->lists->interestCategory->interests->getAll($listId,$id);
                            // Mage::log($this->_groups,null,"idm2.log",true);
                        }
                        
                        /*$group_info_name=array();
                        $group_info_id[]=array();
                        foreach($group_info_array['interests'] as $o)
                        {
                            $group_info_name[]=$o['name'];
                            $group_info_id[]=$o['id'];
                           
                        }*/
                    }
                    
                    //$this->_groups = $api->lists->getLists(null, 'lists', null, 100);
                   
                }
        }
    }
   
    public function toOptionArray()
    {
        $groups = array();

        if (is_array($this->_groups)) {
            $groups[] = array('value' => '', 'label' => Mage::helper('mailchimp')->__('--- Select a Group ---'));
            foreach ($this->_groups['interests'] as $group) {
                $groups [] = array('value' => $group['id'], 'label' => $group['name']);
            }
        } else {
            $groups [] = array('value' => '', 'label' => Mage::helper('mailchimp')->__('--- No data ---'));
        }
 
        return $groups;
    }

}
