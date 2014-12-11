<?php

class Lipscore_RatingsReviews_Model_System_Config_Source_Locale
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'auto', 'label' => Mage::helper('adminhtml')->__('Auto')),
            array('value' => 'en',   'label' => Mage::helper('adminhtml')->__('English')),
            array('value' => 'it',   'label' => Mage::helper('adminhtml')->__('Italian')),
            array('value' => 'no',   'label' => Mage::helper('adminhtml')->__('Norwegian')),
        );
    }
}
