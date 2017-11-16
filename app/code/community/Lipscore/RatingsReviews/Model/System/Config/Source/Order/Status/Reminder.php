<?php
class Lipscore_RatingsReviews_Model_System_Config_Source_Order_Status_Reminder extends
    Lipscore_RatingsReviews_Model_System_Config_Source_Order_Status_Abstract
{
    public function toOptionArray()
    {
        $options = array(
            array('value' =>'', 'label' => Mage::helper('adminhtml')->__('Disable the Review Request Email'))
        );

        try {
            $options = array_merge($options, $this->collectStatuses());
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }

        return $options;
    }
}
