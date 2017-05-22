<?php
class Angara_Productbase_Block_Adminhtml_Pricing_Form extends Mage_Adminhtml_Block_Widget_Form 
{
    protected function _prepareForm() {
		
		$data = Mage::getSingleton('core/session')->getPricingData();
		
		

		$edit = false;
		if (is_array($data)) {
			$edit = true;
		}
		
        $form = new Varien_Data_Form(
            array(
                'id'      => 'edit_form',
                'action'  => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method'  => 'post',
                'enctype' => 'multipart/form-data'
            )
        );
		
		$fieldset = $form->addFieldset('general', array('legend' => $this->__('Product Information')));
		
		# sku field
        $fieldset->addField('sku', 'text', array(
                'label'    => $this->__('SKU'),
                'name'     => 'sku',
                'note'   => $this->__('SKU of product will be generated automatically. Please leave it blank.'),
				'required' => false,
				'disabled' => true,
				'readonly' => true,
            ));
		
		$fieldset->addField('name', 'text', array(
                'label'    => $this->__('Name'),
                'name'     => 'name',
                'note'   => $this->__('Name of the product(optional). It can be visible to customer so choose accordingly or leave it blank.'),
				'required' => false
            ));
			
		$fieldset->addField('returnable', 'select', array(
          'label'     => 'Returnable',
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'returnable',
          'onclick' => "",
          'onchange' => "",
          'values' => array('No' => 'No', 'Yes' => 'Yes'),
          'tabindex' => 1
        ));
		
		$fieldset->addField('restocking', 'select', array(
          'label'     => 'Restocking Fee Taken',
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'restocking',
          'onclick' => "",
          'onchange' => "",
          'values' => array('No' => 'No', 'Yes' => 'Yes'),
          'tabindex' => 1
        ));
		
		$categories = Mage::getModel('productbase/category')->getCollection()->getItems();
		$categoryOptions = array("none"=>"Please Select");
		foreach($categories as $category){
			$categoryOptions[$category->getId()] = $category->getTitle();
		}
		
		
		$fieldset->addField('category', 'select', array(
          'label'     => 'Category',
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'category',
		  'note'   => $this->__('Use it to organise custom build products[Optional], it will not be shown to customer.'),
          'onclick' => "",
          'onchange' => "",
          'values' => $categoryOptions,
          'tabindex' => 1
        ));
		
		$_freeProductCollection = Mage::getResourceModel('catalog/product_collection')
			->addAttributeToSelect('name')
			->addAttributeToFilter('SKU', array('like'=>array('fr%')))
			->addAttributeToFilter('status',1);
			
		$_freeProductCollection->load();
		
		$_freeProductOptions = array(0=>"Please Select");
		foreach($_freeProductCollection as $_freeProduct){
			$_freeProductOptions[$_freeProduct->getId()] = $_freeProduct->getName();
		}
		
		$fieldset->addField('freesku1', 'select', array(
          'label'     => 'Free Product 1',
          'required'  => false,
          'name'      => 'freesku1',
		  'note'   => $this->__('Use it to add free product 1 (optional).'),
          'onclick' => "",
          'onchange' => "",
          'values' => $_freeProductOptions,
          'tabindex' => 1
        ));
		
		$fieldset->addField('freesku2', 'select', array(
          'label'     => 'Free Product 2',
          'required'  => false,
          'name'      => 'freesku2',
		  'note'   => $this->__('Use it to add free product 2 (optional).'),
          'onclick' => "",
          'onchange' => "",
          'values' => $_freeProductOptions,
          'tabindex' => 1
        ));
		
		$fieldset->addField('freesku3', 'select', array(
          'label'     => 'Free Product 3',
          'required'  => false,
          'name'      => 'freesku3',
		  'note'   => $this->__('Use it to add free product 3 (optional).'),
          'onclick' => "",
          'onchange' => "",
          'values' => $_freeProductOptions,
          'tabindex' => 1
        ));
		
		$fieldset->addField('comments', 'textarea', array(
                'label'    => $this->__('Comments'),
                'name'     => 'comments',
                'note'   => $this->__('Write details for your own purpose, it will not be shown to customer.'),
				'required' => true,
				'style' => 'height:84px'
            ));
			
		$priceFieldset = $form->addFieldset('pricing', array('legend' => $this->__('Price Information')));
		$priceFieldset->addField('price', 'text', array(
                'label'    => $this->__('Sell Price $'),
                'name'     => 'price',
                'note'   => $this->__('Enter price manually and please double check the price is correct e.g. 1599'),
				'required' => true,
            ));
			
		$addonsFieldset = $form->addFieldset('addons', array('legend' => $this->__('Add-ons')));
		$addonsFieldset->addField('easyopt', 'select', array(
          'label'     => 'Easy Pay Installments',
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'easyopt',
		  'note'   => $this->__('Use it to allow customer to pay the above price in 2 or 3 equal monthly Interest FREE installments (Optional).'),
          'onclick' => "",
          'onchange' => "",
          'values' => array(0=>'No Easy Pay.', 1 => '2 Easy Payments.', 2 => '3 Easy Payments.'),
          'tabindex' => 1
        ));
		
		$addonsFieldset->addField('appraisal', 'select', array(
          'label'     => 'Jewelry Appraisal',
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'appraisal',
		  'note'   => $this->__('Use it to add jewelry appraisal price in the above mentioned price (Optional).'),
          'onclick' => "",
          'onchange' => "",
          'values' => array(0 => 'No', 1 => 'Yes'),
          'tabindex' => 1
        ));
		
		$addonsFieldset->addField('insurance', 'select', array(
          'label'     => 'Jewelry Insurance',
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'insurance',
		  'note'   => $this->__('Use it to add Warranty price in the above mentioned price (Optional). Warranty price will be calculated according to Angara Set Rules.'),
          'onclick' => "",
          'onchange' => "",
          'values' => array(0 => 'No', 1 => 'Yes'),
          'tabindex' => 1
        ));
		
		$jewelryInformationFieldset = $form->addFieldset('jewelry_information', array('legend' => $this->__('Jewelry Information(Optional for internal use only.)')));
		$jewelryInformationFieldset->addField('stone_information', 'textarea', array(
                'label'    => $this->__('Stones Information'),
                'name'     => 'stone_information',
                'note'   => $this->__('Write stones detail(shape, name, grade, count, setting etc.) for your own purpose, it will not be shown to customer.'),
				'required' => false,
				'style' => 'height:84px'
            ));
		
		$jewelryInformationFieldset->addField('metal_information', 'textarea', array(
                'label'    => $this->__('Metals Information'),
                'name'     => 'metal_information',
                'note'   => $this->__('Write metals detail for your own purpose, it will not be shown to customer.'),
				'required' => false,
				'style' => 'height:84px'
            ));
			
		$jewelryInformationFieldset->addField('other_information', 'textarea', array(
                'label'    => $this->__('Extra Information'),
                'name'     => 'other_information',
                'note'   => $this->__('Write extra details for your own purpose like Ring size, design, any additional alterations etc.'),
				'required' => false,
				'style' => 'height:84px'
            ));
		
		$jewelryInformationFieldset->addField('engraving', 'text', array(
                'label'    => $this->__('Engraving Text + Font '),
                'name'     => 'engraving',
                'note'   => $this->__('(optional) Engraving text + font. Not visible to customer.'),
				'required' => false
            ));
		
		if($edit){
        	//$form->setValues($data);
		}
		
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}