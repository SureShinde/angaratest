<?php
class Angara_ArchiveProduct_Model_Observer
{
	public function archiveActionAdd(Varien_Event_Observer $observer)
	{
		$massBlock = $observer->getEvent()->getBlock();
		/* Archive @Asheesh */
		$massBlock->getMassactionBlock()->addItem('archive', array(
             'label'=> Mage::helper('catalog')->__('Archive'),
             'url'  => $massBlock->getUrl('/adminhtml_archiveproduct/massArchive'),
             'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));
		/* Archive @Asheesh */
		
        return $this;
	}
}