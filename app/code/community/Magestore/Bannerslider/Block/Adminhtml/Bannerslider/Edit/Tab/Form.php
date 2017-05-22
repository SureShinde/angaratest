<?php

class Magestore_Bannerslider_Block_Adminhtml_Bannerslider_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('bannerslider_form', array('legend'=>Mage::helper('bannerslider')->__('General information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('bannerslider')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
			
	if (!Mage::app()->isSingleStoreMode()) {
		$fieldset->addField('store_id', 'multiselect', array(
					'name'      => 'stores[]',
					'label'     => Mage::helper('cms')->__('Store View'),
					'title'     => Mage::helper('cms')->__('Store View'),
					'required'  => true,
					'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
			));
	}
	else {
			$fieldset->addField('store_id', 'hidden', array(
					'name'      => 'stores[]',
					'value'     => Mage::app()->getStore(true)->getId()
			));
			//$model->setStoreId(Mage::app()->getStore(true)->getId());
	}

      $fieldset->addField('filename', 'image', array(
          'label'     => Mage::helper('bannerslider')->__('Image File'),
          'required'  => true,
          'name'      => 'filename',
	  ));
	  
	  	//	S:VA
		$fieldset->addField('image_position', 'select', array(
            'label' => Mage::helper('bannerslider')->__('Image Position'),
            'class' => 'required-entry',
			'required'  => true,
            'name' => 'image_position',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => 1,
                ),
                array(
                    'value' => 2,
                    'label' => 2,
                ), 
				 array(
                    'value' => 3,
                    'label' => 3,
                ), 
				 array(
                    'value' => 4,
                    'label' => 4,
                ), 
				 array(
                    'value' => 5,
                    'label' => 5,
                ),   
            ),
		));
		
		/*$fieldset->addField('image_type', 'select', array(
            'label' => Mage::helper('bannerslider')->__('Image Type'),
            'class' => 'required-entry',
			'required'  => true,
            'name' => 'image_type',
            'values' => array(
                array(
                    'value' => 4,
                    'label' => Mage::helper('bannerslider')->__('Type 1 with Square Image'),
                ),
                array(
                    'value' => 3,
                    'label' => Mage::helper('bannerslider')->__('Type 2 with Horizontal Image'),
                ),                
            ),
        ));*/
		
		$fieldset->addField('image_alt', 'text', array(
			'label'     => Mage::helper('bannerslider')->__('Image Alt Text'),
			'required'  => false,
			'name'      => 'image_alt',
		));
		//	E:VA
			
	  $fieldset->addField('is_home', 'select', array(
          'label'     => Mage::helper('bannerslider')->__('Show in'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'is_home',
		  'values'	=> Mage::helper('bannerslider')->getDisplayOption(),
      ));
	  
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('bannerslider')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('bannerslider')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('bannerslider')->__('Disabled'),
              ),
          ),
      ));
			
		$fieldset->addField('weblink', 'text', array(
			'label'     => Mage::helper('bannerslider')->__('Web Url'),
			'required'  => true,
			'name'      => 'weblink',
		));
			
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('bannerslider')->__('Content'),
          'title'     => Mage::helper('bannerslider')->__('Content'),
          'style'     => 'width:280px; height:100px;',
          'wysiwyg'   => false,
          'required'  => false,
      ));
			
     
      if ( Mage::getSingleton('adminhtml/session')->getBannerSliderData() )
      {
          $data = Mage::getSingleton('adminhtml/session')->getBannerSliderData();
          Mage::getSingleton('adminhtml/session')->setBannerSliderData(null);
      } elseif ( Mage::registry('bannerslider_data') ) {
          $data = Mage::registry('bannerslider_data')->getData();
      }
	  $data['store_id'] = explode(',',$data['stores']);
	  $form->setValues($data);
	  
      return parent::_prepareForm();
  }
}