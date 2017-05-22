<?php
class Angara_Gemstonecolor_Block_Adminhtml_Catalog_Product_Edit_Tab
extends Mage_Adminhtml_Block_Template
implements Mage_Adminhtml_Block_Widget_Tab_Interface 
{
   /**
     * Retrieve the label used for the tab relating to this block
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Gemstone Colors');
    }
   /**
     * Retrieve the title used by this tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Click here to set different Gemstone Colors');
    }
	
	public function getTabUrl() {
		return $this->getUrl('*/adminhtml_gemstonecolor/gemstonecolor',array('_current'=>true));
	}
	
	public function getTabClass() {
		return 'ajax';
	}
	
   /**
     * Determines whether to display the tab
     * Add logic here to decide whether you want the tab to display
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }
    /**
     * Stops the tab being hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}