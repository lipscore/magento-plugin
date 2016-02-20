<?php
class Lipscore_RatingsReviews_Model_System_Config_Source_Reminderperiod
{
    public function toOptionArray()
    {
        return array(
            array('value' =>'', 'label' => Mage::helper('adminhtml')->__('--Please Select--')),
            array('value' => '1 week',   'label' => Mage::helper('adminhtml')->__('1 week')),
            array('value' => '1 month',  'label' => Mage::helper('adminhtml')->__('1 month')),
            array('value' => '3 months', 'label' => Mage::helper('adminhtml')->__('3 months')),
        );
    }
}
