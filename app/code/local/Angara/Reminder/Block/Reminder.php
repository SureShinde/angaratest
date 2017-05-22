<?php
class Angara_Reminder_Block_Reminder extends Mage_Core_Block_Template
{
	protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')
            ->setTitle(Mage::helper('customer')->__('My Reminders'));

        return parent::_prepareLayout();
    }

    public function getAddReminderUrl()
    {
        return $this->getUrl('reminder/index/new', array('_secure'=>true));
    }

    public function getBackUrl()
    {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/', array('_secure'=>true));
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('reminder/index/delete', array('_secure'=>true));
    }
	
	public function getSaveUrl()
    {
        return $this->getUrl('reminder/index/new', array('_secure'=>true));
    }
	
	public function getUpdateUrl($reminder_id=NULL)
    {
        return $this->getUrl('reminder/index/edit', array('_secure'=>true, 'id'=>$reminder_id));
    }
		
    public function getEditUrl($reminder_id=NULL)
    {
        return $this->getUrl('reminder/index/edit', array('_secure'=>true, 'id'=>$reminder_id));
    }

    public function getReminderHtml($reminder)
    {
        return $reminder->format('html');
    }

    public function getCustomer()
    {
        $customer = $this->getData('customer');
        if (is_null($customer)) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $this->setData('customer', $customer);
        }
        return $customer;
    }
	
	public function getRelationshipOptions()
    {
        $relationship_arr = array(
			''=>'Please Select Relation',
			'Boyfriend'=>'Boyfriend',
			'Brother'=>'Brother',
			'Sister'=>'Sister',
			'Other'=>'Other',
			'Self'=>'Self',
			'GiveAFreeGift'=>'GiveAFreeGift',
			'Father'=>'Father',
			'Friend'=>'Friend',
			'Fiance'=>'Fiance',
			'Wife'=>'Wife',
			'Girlfriend'=>'Girlfriend',
			'Mother'=>'Mother',
			'Grand-parent'=>'Grand-parent',
			'Daughter'=>'Daughter',
			'Co-worker'=>'Co-worker',
			'Son'=>'Son',
			'Husband'=>'Husband'
		);
		return $relationship_arr; 
    }
	
	public function getOccasionOptions()
    {
        $occasion_arr = array(
			""=>"Please Select Occasion",
			"Anniversary"=>"Anniversary",
			"Bat/Bar Mitzvah"=>"Bat/Bar Mitzvah",
			"Bereavement"=>"Bereavement",
			"Birthday"=>"Birthday",
			"Celebration"=>"Celebration",
			"Christmas"=>"Christmas",
			"Communion"=>"Communion",
			"Easter"=>"Easter",
			"Engagement"=>"Engagement",
			"Father's Day"=>"Father's Day",
			"Graduation"=>"Graduation",
			"Grand-parents Day"=>"Grand-parents Day",
			"Hanukkah"=>"Hanukkah",
			"Mother's Day"=>"Mother's Day",
			"New Baby"=>"New Baby",
			"Other"=>"Other",
			"Promotion"=>"Promotion",
			"Valentine's Day"=>"Valentine's Day"
		);
		return $occasion_arr; 
    }
	
	public function getRingOptions()
    {
        $ring_arr = array(
			""=>"Please Select Ring Size",
			"3"=>"3",
			"3.5"=>"3.5",
			"4"=>"4",
			"4.5"=>"4.5",
			"5"=>"5",
			"5.5"=>"5.5",
			"6"=>"6",
			"6.5"=>"6.5",
			"7"=>"7",
			"7.5"=>"7.5",
			"8"=>"8",
			"8.5"=>"8.5",
			"9"=>"9",
			"9.5"=>"9.5",
			"10"=>"10",
			"10.5"=>"10.5",
			"11"=>"11",
			"11.5"=>"11.5",
			"12"=>"12",
			"12.5"=>"12.5",
			"13"=>"13"
		);
		return $ring_arr; 
    }
	
	public function getGemstoneOptions()
    {
        $gemstone_arr = array(
			""=>"Please Select Gemstone",
			"Sapphire"=>"Sapphire",
			"Tanzanite"=>"Tanzanite",
			"Emerald"=>"Emerald",
			"Ruby"=>"Ruby",
			"Aquamarine"=>"Aquamarine",
			"Pink Sapphire"=>"Pink Sapphire",
			"Lab Created Ruby"=>"Lab Created Ruby",
			"Diamond"=>"Diamond",
			"Lab Created Sapphire"=>"Lab Created Sapphire",
			"Lab Created Emerald"=>"Lab Created Emerald",
			"Amethyst"=>"Amethyst",
			"Citrine"=>"Citrine",
			"Pink Tourmaline"=>"Pink Tourmaline",
			"Peridot"=>"Peridot",
			"Garnet"=>"Garnet"
		);
		return $gemstone_arr; 
    }	
}