<?php

class Lipscore_RatingsReviews_Model_System_Config_Source_Locale
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'auto', 'label' => Mage::helper('adminhtml')->__('Auto')),
            array('value' => 'cz',   'label' => Mage::helper('adminhtml')->__('Czech')),
            array('value' => 'nl',   'label' => Mage::helper('adminhtml')->__('Dutch')),
            array('value' => 'en',   'label' => Mage::helper('adminhtml')->__('English')),
            array('value' => 'it',   'label' => Mage::helper('adminhtml')->__('Italian')),
            array('value' => 'no',   'label' => Mage::helper('adminhtml')->__('Norwegian')),
            array('value' => 'br',   'label' => Mage::helper('adminhtml')->__('Portuguese (Brazil)')),
            array('value' => 'ru',   'label' => Mage::helper('adminhtml')->__('Russian')),
            array('value' => 'es',   'label' => Mage::helper('adminhtml')->__('Spanish')),
            array('value' => 'se',   'label' => Mage::helper('adminhtml')->__('Swedish')),
        );
    }
}
