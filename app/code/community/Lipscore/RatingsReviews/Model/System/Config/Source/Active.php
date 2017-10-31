<?php

class Lipscore_RatingsReviews_Model_System_Config_Source_Active
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Active')),
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Inactive')),
        );
    }
}
