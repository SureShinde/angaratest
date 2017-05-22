<?php

class Angara_Feeds_Block_Adminhtml_Feeds_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('feeds_form', array('legend'=>Mage::helper('feeds')->__('General information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('feeds')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
			
		/*if (!Mage::app()->isSingleStoreMode()) {
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
					$model->setStoreId(Mage::app()->getStore(true)->getId());
			}*/

      $fieldset->addField('marketplace', 'text', array(
          'label'     => Mage::helper('feeds')->__('Marketplace'),
		  'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'marketplace',
	  ));
		
	$fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('feeds')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('feeds')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('feeds')->__('Disabled'),
              ),
          ),
      ));	
	 
	 $fieldset->addField('headings1', 'textarea', array(
          'label'     => Mage::helper('feeds')->__('Headings 1 (in comma seprated)'),
           'required'  => false,
          'name'      => 'headings1',
      ));
	  
	  $fieldset->addField('headings2', 'textarea', array(
          'label'     => Mage::helper('feeds')->__('Headings 2 (in comma seprated)'),
           'required'  => false,
          'name'      => 'headings2',
      ));
	  
	  $fieldset->addField('headings3', 'textarea', array(
          'label'     => Mage::helper('feeds')->__('Headings 3 (in comma seprated)'),
           'required'  => false,
          'name'      => 'headings3',
      ));
	 
	 $fieldset->addField('column_1', 'text', array(
          'label'     => Mage::helper('feeds')->__('Column 1 Value'),
           'required'  => false,
          'name'      => 'column_1',
      ));
	  
	  $fieldset->addField('column_2', 'text', array(
          'label'     => Mage::helper('feeds')->__('Column 2 Value'),
           'required'  => false,
          'name'      => 'column_2',
      ));
	  
	  $fieldset->addField('column_3', 'text', array(
          'label'     => Mage::helper('feeds')->__('Column 3 Value'),
           'required'  => false,
          'name'      => 'column_3',
      ));
	  
	  $fieldset->addField('column_4', 'text', array(
          'label'     => Mage::helper('feeds')->__('Column 4 Value'),
           'required'  => false,
          'name'      => 'column_4',
      ));
	  
	  $fieldset->addField('column_5', 'text', array(
          'label'     => Mage::helper('feeds')->__('Column 5 Value'),
           'required'  => false,
          'name'      => 'column_5',
      ));
	  
	  $fieldset->addField('column_6', 'text', array(
          'label'     => Mage::helper('feeds')->__('Column 6 Value'),
           'required'  => false,
          'name'      => 'column_6',
      ));
	  
	  $fieldset->addField('column_7', 'text', array(
          'label'     => Mage::helper('feeds')->__('Column 7 Value'),
           'required'  => false,
          'name'      => 'column_7',
      ));
	  
	  $fieldset->addField('column_8', 'text', array(
          'label'     => Mage::helper('feeds')->__('Column 8 Value'),
           'required'  => false,
          'name'      => 'column_8',
      ));
	  
	  $fieldset->addField('column_9', 'text', array(
          'label'     => Mage::helper('feeds')->__('Column 9 Value'),
           'required'  => false,
          'name'      => 'column_9',
      ));
	  
	  $fieldset->addField('column_10', 'text', array(
          'label'     => Mage::helper('feeds')->__('Column 10 Value'),
           'required'  => false,
          'name'      => 'column_10',
      ));
	 
	 $fieldset->addField('column_11', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 11 Value'),
		 'required'  => false,
		'name'      => 'column_11',
		));
		$fieldset->addField('column_12', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 12 Value'),
		 'required'  => false,
		'name'      => 'column_12',
		));
		$fieldset->addField('column_13', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 13 Value'),
		 'required'  => false,
		'name'      => 'column_13',
		));
		$fieldset->addField('column_14', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 14 Value'),
		 'required'  => false,
		'name'      => 'column_14',
		));
		$fieldset->addField('column_15', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 15 Value'),
		 'required'  => false,
		'name'      => 'column_15',
		));
		$fieldset->addField('column_16', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 16 Value'),
		 'required'  => false,
		'name'      => 'column_16',
		));
		$fieldset->addField('column_17', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 17 Value'),
		 'required'  => false,
		'name'      => 'column_17',
		));
		$fieldset->addField('column_18', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 18 Value'),
		 'required'  => false,
		'name'      => 'column_18',
		));
		$fieldset->addField('column_19', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 19 Value'),
		 'required'  => false,
		'name'      => 'column_19',
		));
		$fieldset->addField('column_20', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 20 Value'),
		 'required'  => false,
		'name'      => 'column_20',
		));
		$fieldset->addField('column_21', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 21 Value'),
		 'required'  => false,
		'name'      => 'column_21',
		));
		$fieldset->addField('column_22', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 22 Value'),
		 'required'  => false,
		'name'      => 'column_22',
		));
		$fieldset->addField('column_23', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 23 Value'),
		 'required'  => false,
		'name'      => 'column_23',
		));
		$fieldset->addField('column_24', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 24 Value'),
		 'required'  => false,
		'name'      => 'column_24',
		));
		$fieldset->addField('column_25', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 25 Value'),
		 'required'  => false,
		'name'      => 'column_25',
		));
		$fieldset->addField('column_26', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 26 Value'),
		 'required'  => false,
		'name'      => 'column_26',
		));
		$fieldset->addField('column_27', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 27 Value'),
		 'required'  => false,
		'name'      => 'column_27',
		));
		$fieldset->addField('column_28', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 28 Value'),
		 'required'  => false,
		'name'      => 'column_28',
		));
		$fieldset->addField('column_29', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 29 Value'),
		 'required'  => false,
		'name'      => 'column_29',
		));
		$fieldset->addField('column_30', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 30 Value'),
		 'required'  => false,
		'name'      => 'column_30',
		));
		$fieldset->addField('column_31', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 31 Value'),
		 'required'  => false,
		'name'      => 'column_31',
		));
		$fieldset->addField('column_32', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 32 Value'),
		 'required'  => false,
		'name'      => 'column_32',
		));
		$fieldset->addField('column_33', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 33 Value'),
		 'required'  => false,
		'name'      => 'column_33',
		));
		$fieldset->addField('column_34', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 34 Value'),
		 'required'  => false,
		'name'      => 'column_34',
		));
		$fieldset->addField('column_35', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 35 Value'),
		 'required'  => false,
		'name'      => 'column_35',
		));
		$fieldset->addField('column_36', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 36 Value'),
		 'required'  => false,
		'name'      => 'column_36',
		));
		$fieldset->addField('column_37', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 37 Value'),
		 'required'  => false,
		'name'      => 'column_37',
		));
		$fieldset->addField('column_38', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 38 Value'),
		 'required'  => false,
		'name'      => 'column_38',
		));
		$fieldset->addField('column_39', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 39 Value'),
		 'required'  => false,
		'name'      => 'column_39',
		));
		$fieldset->addField('column_40', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 40 Value'),
		 'required'  => false,
		'name'      => 'column_40',
		));
		$fieldset->addField('column_41', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 41 Value'),
		 'required'  => false,
		'name'      => 'column_41',
		));
		$fieldset->addField('column_42', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 42 Value'),
		 'required'  => false,
		'name'      => 'column_42',
		));
		$fieldset->addField('column_43', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 43 Value'),
		 'required'  => false,
		'name'      => 'column_43',
		));
		$fieldset->addField('column_44', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 44 Value'),
		 'required'  => false,
		'name'      => 'column_44',
		));
		$fieldset->addField('column_45', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 45 Value'),
		 'required'  => false,
		'name'      => 'column_45',
		));
		$fieldset->addField('column_46', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 46 Value'),
		 'required'  => false,
		'name'      => 'column_46',
		));
		$fieldset->addField('column_47', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 47 Value'),
		 'required'  => false,
		'name'      => 'column_47',
		));
		$fieldset->addField('column_48', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 48 Value'),
		 'required'  => false,
		'name'      => 'column_48',
		));
		$fieldset->addField('column_49', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 49 Value'),
		 'required'  => false,
		'name'      => 'column_49',
		));
		$fieldset->addField('column_50', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 50 Value'),
		 'required'  => false,
		'name'      => 'column_50',
		));
		$fieldset->addField('column_51', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 51 Value'),
		 'required'  => false,
		'name'      => 'column_51',
		));
		$fieldset->addField('column_52', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 52 Value'),
		 'required'  => false,
		'name'      => 'column_52',
		));
		$fieldset->addField('column_53', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 53 Value'),
		 'required'  => false,
		'name'      => 'column_53',
		));
		$fieldset->addField('column_54', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 54 Value'),
		 'required'  => false,
		'name'      => 'column_54',
		));
		$fieldset->addField('column_55', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 55 Value'),
		 'required'  => false,
		'name'      => 'column_55',
		));
		$fieldset->addField('column_56', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 56 Value'),
		 'required'  => false,
		'name'      => 'column_56',
		));
		$fieldset->addField('column_57', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 57 Value'),
		 'required'  => false,
		'name'      => 'column_57',
		));
		$fieldset->addField('column_58', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 58 Value'),
		 'required'  => false,
		'name'      => 'column_58',
		));
		$fieldset->addField('column_59', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 59 Value'),
		 'required'  => false,
		'name'      => 'column_59',
		));
		$fieldset->addField('column_60', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 60 Value'),
		 'required'  => false,
		'name'      => 'column_60',
		));
		$fieldset->addField('column_61', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 61 Value'),
		 'required'  => false,
		'name'      => 'column_61',
		));
		$fieldset->addField('column_62', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 62 Value'),
		 'required'  => false,
		'name'      => 'column_62',
		));
		$fieldset->addField('column_63', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 63 Value'),
		 'required'  => false,
		'name'      => 'column_63',
		));
		$fieldset->addField('column_64', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 64 Value'),
		 'required'  => false,
		'name'      => 'column_64',
		));
		$fieldset->addField('column_65', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 65 Value'),
		 'required'  => false,
		'name'      => 'column_65',
		));
		$fieldset->addField('column_66', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 66 Value'),
		 'required'  => false,
		'name'      => 'column_66',
		));
		$fieldset->addField('column_67', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 67 Value'),
		 'required'  => false,
		'name'      => 'column_67',
		));
		$fieldset->addField('column_68', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 68 Value'),
		 'required'  => false,
		'name'      => 'column_68',
		));
		$fieldset->addField('column_69', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 69 Value'),
		 'required'  => false,
		'name'      => 'column_69',
		));
		$fieldset->addField('column_70', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 70 Value'),
		 'required'  => false,
		'name'      => 'column_70',
		));
		$fieldset->addField('column_71', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 71 Value'),
		 'required'  => false,
		'name'      => 'column_71',
		));
		$fieldset->addField('column_72', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 72 Value'),
		 'required'  => false,
		'name'      => 'column_72',
		));
		$fieldset->addField('column_73', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 73 Value'),
		 'required'  => false,
		'name'      => 'column_73',
		));
		$fieldset->addField('column_74', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 74 Value'),
		 'required'  => false,
		'name'      => 'column_74',
		));
		$fieldset->addField('column_75', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 75 Value'),
		 'required'  => false,
		'name'      => 'column_75',
		));
		$fieldset->addField('column_76', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 76 Value'),
		 'required'  => false,
		'name'      => 'column_76',
		));
		$fieldset->addField('column_77', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 77 Value'),
		 'required'  => false,
		'name'      => 'column_77',
		));
		$fieldset->addField('column_78', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 78 Value'),
		 'required'  => false,
		'name'      => 'column_78',
		));
		$fieldset->addField('column_79', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 79 Value'),
		 'required'  => false,
		'name'      => 'column_79',
		));
		$fieldset->addField('column_80', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 80 Value'),
		 'required'  => false,
		'name'      => 'column_80',
		));
		$fieldset->addField('column_81', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 81 Value'),
		 'required'  => false,
		'name'      => 'column_81',
		));
		$fieldset->addField('column_82', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 82 Value'),
		 'required'  => false,
		'name'      => 'column_82',
		));
		$fieldset->addField('column_83', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 83 Value'),
		 'required'  => false,
		'name'      => 'column_83',
		));
		$fieldset->addField('column_84', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 84 Value'),
		 'required'  => false,
		'name'      => 'column_84',
		));
		$fieldset->addField('column_85', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 85 Value'),
		 'required'  => false,
		'name'      => 'column_85',
		));
		$fieldset->addField('column_86', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 86 Value'),
		 'required'  => false,
		'name'      => 'column_86',
		));
		$fieldset->addField('column_87', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 87 Value'),
		 'required'  => false,
		'name'      => 'column_87',
		));
		$fieldset->addField('column_88', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 88 Value'),
		 'required'  => false,
		'name'      => 'column_88',
		));
		$fieldset->addField('column_89', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 89 Value'),
		 'required'  => false,
		'name'      => 'column_89',
		));
		$fieldset->addField('column_90', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 90 Value'),
		 'required'  => false,
		'name'      => 'column_90',
		));
		$fieldset->addField('column_91', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 91 Value'),
		 'required'  => false,
		'name'      => 'column_91',
		));
		$fieldset->addField('column_92', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 92 Value'),
		 'required'  => false,
		'name'      => 'column_92',
		));
		$fieldset->addField('column_93', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 93 Value'),
		 'required'  => false,
		'name'      => 'column_93',
		));
		$fieldset->addField('column_94', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 94 Value'),
		 'required'  => false,
		'name'      => 'column_94',
		));
		$fieldset->addField('column_95', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 95 Value'),
		 'required'  => false,
		'name'      => 'column_95',
		));
		$fieldset->addField('column_96', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 96 Value'),
		 'required'  => false,
		'name'      => 'column_96',
		));
		$fieldset->addField('column_97', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 97 Value'),
		 'required'  => false,
		'name'      => 'column_97',
		));
		$fieldset->addField('column_98', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 98 Value'),
		 'required'  => false,
		'name'      => 'column_98',
		));
		$fieldset->addField('column_99', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 99 Value'),
		 'required'  => false,
		'name'      => 'column_99',
		));
		$fieldset->addField('column_100', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 100 Value'),
		 'required'  => false,
		'name'      => 'column_100',
		));
		$fieldset->addField('column_101', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 101 Value'),
		 'required'  => false,
		'name'      => 'column_101',
		));
		$fieldset->addField('column_102', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 102 Value'),
		 'required'  => false,
		'name'      => 'column_102',
		));
		$fieldset->addField('column_103', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 103 Value'),
		 'required'  => false,
		'name'      => 'column_103',
		));
		$fieldset->addField('column_104', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 104 Value'),
		 'required'  => false,
		'name'      => 'column_104',
		));
		$fieldset->addField('column_105', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 105 Value'),
		 'required'  => false,
		'name'      => 'column_105',
		));
		$fieldset->addField('column_106', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 106 Value'),
		 'required'  => false,
		'name'      => 'column_106',
		));
		$fieldset->addField('column_107', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 107 Value'),
		 'required'  => false,
		'name'      => 'column_107',
		));
		$fieldset->addField('column_108', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 108 Value'),
		 'required'  => false,
		'name'      => 'column_108',
		));
		$fieldset->addField('column_109', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 109 Value'),
		 'required'  => false,
		'name'      => 'column_109',
		));
		$fieldset->addField('column_110', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 110 Value'),
		 'required'  => false,
		'name'      => 'column_110',
		));
		$fieldset->addField('column_111', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 111 Value'),
		 'required'  => false,
		'name'      => 'column_111',
		));
		$fieldset->addField('column_112', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 112 Value'),
		 'required'  => false,
		'name'      => 'column_112',
		));
		$fieldset->addField('column_113', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 113 Value'),
		 'required'  => false,
		'name'      => 'column_113',
		));
		$fieldset->addField('column_114', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 114 Value'),
		 'required'  => false,
		'name'      => 'column_114',
		));
		$fieldset->addField('column_115', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 115 Value'),
		 'required'  => false,
		'name'      => 'column_115',
		));
		$fieldset->addField('column_116', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 116 Value'),
		 'required'  => false,
		'name'      => 'column_116',
		));
		$fieldset->addField('column_117', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 117 Value'),
		 'required'  => false,
		'name'      => 'column_117',
		));
		$fieldset->addField('column_118', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 118 Value'),
		 'required'  => false,
		'name'      => 'column_118',
		));
		$fieldset->addField('column_119', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 119 Value'),
		 'required'  => false,
		'name'      => 'column_119',
		));
		$fieldset->addField('column_120', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 120 Value'),
		 'required'  => false,
		'name'      => 'column_120',
		));
		$fieldset->addField('column_121', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 121 Value'),
		 'required'  => false,
		'name'      => 'column_121',
		));
		$fieldset->addField('column_122', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 122 Value'),
		 'required'  => false,
		'name'      => 'column_122',
		));
		$fieldset->addField('column_123', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 123 Value'),
		 'required'  => false,
		'name'      => 'column_123',
		));
		$fieldset->addField('column_124', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 124 Value'),
		 'required'  => false,
		'name'      => 'column_124',
		));
		$fieldset->addField('column_125', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 125 Value'),
		 'required'  => false,
		'name'      => 'column_125',
		));
		$fieldset->addField('column_126', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 126 Value'),
		 'required'  => false,
		'name'      => 'column_126',
		));
		$fieldset->addField('column_127', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 127 Value'),
		 'required'  => false,
		'name'      => 'column_127',
		));
		$fieldset->addField('column_128', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 128 Value'),
		 'required'  => false,
		'name'      => 'column_128',
		));
		$fieldset->addField('column_129', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 129 Value'),
		 'required'  => false,
		'name'      => 'column_129',
		));
		$fieldset->addField('column_130', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 130 Value'),
		 'required'  => false,
		'name'      => 'column_130',
		));
		$fieldset->addField('column_131', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 131 Value'),
		 'required'  => false,
		'name'      => 'column_131',
		));
		$fieldset->addField('column_132', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 132 Value'),
		 'required'  => false,
		'name'      => 'column_132',
		));
		$fieldset->addField('column_133', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 133 Value'),
		 'required'  => false,
		'name'      => 'column_133',
		));
		$fieldset->addField('column_134', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 134 Value'),
		 'required'  => false,
		'name'      => 'column_134',
		));
		$fieldset->addField('column_135', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 135 Value'),
		 'required'  => false,
		'name'      => 'column_135',
		));
		$fieldset->addField('column_136', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 136 Value'),
		 'required'  => false,
		'name'      => 'column_136',
		));
		$fieldset->addField('column_137', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 137 Value'),
		 'required'  => false,
		'name'      => 'column_137',
		));
		$fieldset->addField('column_138', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 138 Value'),
		 'required'  => false,
		'name'      => 'column_138',
		));
		$fieldset->addField('column_139', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 139 Value'),
		 'required'  => false,
		'name'      => 'column_139',
		));
		$fieldset->addField('column_140', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 140 Value'),
		 'required'  => false,
		'name'      => 'column_140',
		));
		$fieldset->addField('column_141', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 141 Value'),
		 'required'  => false,
		'name'      => 'column_141',
		));
		$fieldset->addField('column_142', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 142 Value'),
		 'required'  => false,
		'name'      => 'column_142',
		));
		$fieldset->addField('column_143', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 143 Value'),
		 'required'  => false,
		'name'      => 'column_143',
		));
		$fieldset->addField('column_144', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 144 Value'),
		 'required'  => false,
		'name'      => 'column_144',
		));
		$fieldset->addField('column_145', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 145 Value'),
		 'required'  => false,
		'name'      => 'column_145',
		));
		$fieldset->addField('column_146', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 146 Value'),
		 'required'  => false,
		'name'      => 'column_146',
		));
		$fieldset->addField('column_147', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 147 Value'),
		 'required'  => false,
		'name'      => 'column_147',
		));
		$fieldset->addField('column_148', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 148 Value'),
		 'required'  => false,
		'name'      => 'column_148',
		));
		$fieldset->addField('column_149', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 149 Value'),
		 'required'  => false,
		'name'      => 'column_149',
		));
		$fieldset->addField('column_150', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 150 Value'),
		 'required'  => false,
		'name'      => 'column_150',
		));
		$fieldset->addField('column_151', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 151 Value'),
		 'required'  => false,
		'name'      => 'column_151',
		));
		$fieldset->addField('column_152', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 152 Value'),
		 'required'  => false,
		'name'      => 'column_152',
		));
		$fieldset->addField('column_153', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 153 Value'),
		 'required'  => false,
		'name'      => 'column_153',
		));
		$fieldset->addField('column_154', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 154 Value'),
		 'required'  => false,
		'name'      => 'column_154',
		));
		$fieldset->addField('column_155', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 155 Value'),
		 'required'  => false,
		'name'      => 'column_155',
		));
		$fieldset->addField('column_156', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 156 Value'),
		 'required'  => false,
		'name'      => 'column_156',
		));
		$fieldset->addField('column_157', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 157 Value'),
		 'required'  => false,
		'name'      => 'column_157',
		));
		$fieldset->addField('column_158', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 158 Value'),
		 'required'  => false,
		'name'      => 'column_158',
		));
		$fieldset->addField('column_159', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 159 Value'),
		 'required'  => false,
		'name'      => 'column_159',
		));
		$fieldset->addField('column_160', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 160 Value'),
		 'required'  => false,
		'name'      => 'column_160',
		));
		$fieldset->addField('column_161', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 161 Value'),
		 'required'  => false,
		'name'      => 'column_161',
		));
		$fieldset->addField('column_162', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 162 Value'),
		 'required'  => false,
		'name'      => 'column_162',
		));
		$fieldset->addField('column_163', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 163 Value'),
		 'required'  => false,
		'name'      => 'column_163',
		));
		$fieldset->addField('column_164', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 164 Value'),
		 'required'  => false,
		'name'      => 'column_164',
		));
		$fieldset->addField('column_165', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 165 Value'),
		 'required'  => false,
		'name'      => 'column_165',
		));
		$fieldset->addField('column_166', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 166 Value'),
		 'required'  => false,
		'name'      => 'column_166',
		));
		$fieldset->addField('column_167', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 167 Value'),
		 'required'  => false,
		'name'      => 'column_167',
		));
		$fieldset->addField('column_168', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 168 Value'),
		 'required'  => false,
		'name'      => 'column_168',
		));
		$fieldset->addField('column_169', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 169 Value'),
		 'required'  => false,
		'name'      => 'column_169',
		));
		$fieldset->addField('column_170', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 170 Value'),
		 'required'  => false,
		'name'      => 'column_170',
		));
		$fieldset->addField('column_171', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 171 Value'),
		 'required'  => false,
		'name'      => 'column_171',
		));
		$fieldset->addField('column_172', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 172 Value'),
		 'required'  => false,
		'name'      => 'column_172',
		));
		$fieldset->addField('column_173', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 173 Value'),
		 'required'  => false,
		'name'      => 'column_173',
		));
		$fieldset->addField('column_174', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 174 Value'),
		 'required'  => false,
		'name'      => 'column_174',
		));
		$fieldset->addField('column_175', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 175 Value'),
		 'required'  => false,
		'name'      => 'column_175',
		));
		$fieldset->addField('column_176', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 176 Value'),
		 'required'  => false,
		'name'      => 'column_176',
		));
		$fieldset->addField('column_177', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 177 Value'),
		 'required'  => false,
		'name'      => 'column_177',
		));
		$fieldset->addField('column_178', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 178 Value'),
		 'required'  => false,
		'name'      => 'column_178',
		));
		$fieldset->addField('column_179', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 179 Value'),
		 'required'  => false,
		'name'      => 'column_179',
		));
		$fieldset->addField('column_180', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 180 Value'),
		 'required'  => false,
		'name'      => 'column_180',
		));
		$fieldset->addField('column_181', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 181 Value'),
		 'required'  => false,
		'name'      => 'column_181',
		));
		$fieldset->addField('column_182', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 182 Value'),
		 'required'  => false,
		'name'      => 'column_182',
		));
		$fieldset->addField('column_183', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 183 Value'),
		 'required'  => false,
		'name'      => 'column_183',
		));
		$fieldset->addField('column_184', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 184 Value'),
		 'required'  => false,
		'name'      => 'column_184',
		));
		$fieldset->addField('column_185', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 185 Value'),
		 'required'  => false,
		'name'      => 'column_185',
		));
		$fieldset->addField('column_186', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 186 Value'),
		 'required'  => false,
		'name'      => 'column_186',
		));
		$fieldset->addField('column_187', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 187 Value'),
		 'required'  => false,
		'name'      => 'column_187',
		));
		$fieldset->addField('column_188', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 188 Value'),
		 'required'  => false,
		'name'      => 'column_188',
		));
		$fieldset->addField('column_189', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 189 Value'),
		 'required'  => false,
		'name'      => 'column_189',
		));
		$fieldset->addField('column_190', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 190 Value'),
		 'required'  => false,
		'name'      => 'column_190',
		));
		$fieldset->addField('column_191', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 191 Value'),
		 'required'  => false,
		'name'      => 'column_191',
		));
		$fieldset->addField('column_192', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 192 Value'),
		 'required'  => false,
		'name'      => 'column_192',
		));
		$fieldset->addField('column_193', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 193 Value'),
		 'required'  => false,
		'name'      => 'column_193',
		));
		$fieldset->addField('column_194', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 194 Value'),
		 'required'  => false,
		'name'      => 'column_194',
		));
		$fieldset->addField('column_195', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 195 Value'),
		 'required'  => false,
		'name'      => 'column_195',
		));
		$fieldset->addField('column_196', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 196 Value'),
		 'required'  => false,
		'name'      => 'column_196',
		));
		$fieldset->addField('column_197', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 197 Value'),
		 'required'  => false,
		'name'      => 'column_197',
		));
		$fieldset->addField('column_198', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 198 Value'),
		 'required'  => false,
		'name'      => 'column_198',
		));
		$fieldset->addField('column_199', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 199 Value'),
		 'required'  => false,
		'name'      => 'column_199',
		));
		$fieldset->addField('column_200', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 200 Value'),
		 'required'  => false,
		'name'      => 'column_200',
		));
		$fieldset->addField('column_201', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 201 Value'),
		 'required'  => false,
		'name'      => 'column_201',
		));
		$fieldset->addField('column_202', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 202 Value'),
		 'required'  => false,
		'name'      => 'column_202',
		));
		$fieldset->addField('column_203', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 203 Value'),
		 'required'  => false,
		'name'      => 'column_203',
		));
		$fieldset->addField('column_204', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 204 Value'),
		 'required'  => false,
		'name'      => 'column_204',
		));
		$fieldset->addField('column_205', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 205 Value'),
		 'required'  => false,
		'name'      => 'column_205',
		));
		$fieldset->addField('column_206', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 206 Value'),
		 'required'  => false,
		'name'      => 'column_206',
		));
		$fieldset->addField('column_207', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 207 Value'),
		 'required'  => false,
		'name'      => 'column_207',
		));
		$fieldset->addField('column_208', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 208 Value'),
		 'required'  => false,
		'name'      => 'column_208',
		));
		$fieldset->addField('column_209', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 209 Value'),
		 'required'  => false,
		'name'      => 'column_209',
		));
		$fieldset->addField('column_210', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 210 Value'),
		 'required'  => false,
		'name'      => 'column_210',
		));
		$fieldset->addField('column_211', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 211 Value'),
		 'required'  => false,
		'name'      => 'column_211',
		));
		$fieldset->addField('column_212', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 212 Value'),
		 'required'  => false,
		'name'      => 'column_212',
		));
		$fieldset->addField('column_213', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 213 Value'),
		 'required'  => false,
		'name'      => 'column_213',
		));
		$fieldset->addField('column_214', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 214 Value'),
		 'required'  => false,
		'name'      => 'column_214',
		));
		$fieldset->addField('column_215', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 215 Value'),
		 'required'  => false,
		'name'      => 'column_215',
		));
		$fieldset->addField('column_216', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 216 Value'),
		 'required'  => false,
		'name'      => 'column_216',
		));
		$fieldset->addField('column_217', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 217 Value'),
		 'required'  => false,
		'name'      => 'column_217',
		));
		$fieldset->addField('column_218', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 218 Value'),
		 'required'  => false,
		'name'      => 'column_218',
		));
		$fieldset->addField('column_219', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 219 Value'),
		 'required'  => false,
		'name'      => 'column_219',
		));
		$fieldset->addField('column_220', 'text', array(
		'label'     => Mage::helper('feeds')->__('Column 220 Value'),
		 'required'  => false,
		'name'      => 'column_220',
	));
     
      if ( Mage::getSingleton('adminhtml/session')->getFeedsData() )
      {
          $data = Mage::getSingleton('adminhtml/session')->getFeedsData();
          Mage::getSingleton('adminhtml/session')->setFeedsData(null);
      } elseif ( Mage::registry('feeds_data') ) {
          $data = Mage::registry('feeds_data')->getData();
      }
	  $data['store_id'] = explode(',',$data['stores']);
	  $form->setValues($data);
	  
      return parent::_prepareForm();
  }
}