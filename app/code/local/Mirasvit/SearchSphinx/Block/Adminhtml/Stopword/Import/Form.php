<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Sphinx Search Ultimate
 * @version   2.2.9
 * @revision  370
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_SearchSphinx_Block_Adminhtml_Stopword_Import_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getData('action'),
            'method'  => 'post',
            'enctype' => 'multipart/form-data',
        ));

        $general = $form->addFieldset('general', array('legend' => Mage::helper('searchsphinx')->__('Import')));
        
        $general->addField('import', 'hidden', array(
            'name'  => 'import',
            'value' => 1,
        ));

        $general->addField('file', 'select', array(
            'name'     => 'file',
            'label'    => Mage::helper('searchsphinx')->__('Dictionary'),
            'required' => true,
            'values'   => $this->getDictionaries(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $general->addField('store', 'multiselect', array(
                'label'    => Mage::helper('searchindex')->__('Store View'),
                'required' => true,
                'name'     => 'store',
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
            ));
        } else {
            $general->addField('store', 'hidden', array(
                'name'  => 'store',
                'value' => Mage::app()->getStore(true)->getId()
            ));
        }

        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function getDictionaries()
    {
        $values = array();
        $path   = Mage::getBaseDir('var').DS.'sphinx'.DS.'stopwords'.DS;
        if (file_exists($path)) {
            if ($handle = opendir($path)) {
                while (false !== ($entry = readdir($handle))) {
                    if (substr($entry, 0, 1) != '.') {
                        $values[] = array(
                            'label' => $entry,
                            'value' => $path.DS.$entry
                        );
                    }
                }
                closedir($handle);
            }
        }

        return $values;
    }
}