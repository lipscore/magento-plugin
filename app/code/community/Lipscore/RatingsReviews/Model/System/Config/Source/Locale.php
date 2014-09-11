<?php

class Lipscore_RatingsReviews_Model_System_Config_Source_LOcale
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'auto', 'label' => Mage::helper('adminhtml')->__('Auto')),
            array('value' => 'en',   'label' => Mage::helper('adminhtml')->__('English')),
            array('value' => 'no',   'label' => Mage::helper('adminhtml')->__('Norwegian')),
        );
    }
}
