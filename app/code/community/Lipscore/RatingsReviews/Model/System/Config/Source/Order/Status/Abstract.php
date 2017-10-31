<?php
class Lipscore_RatingsReviews_Model_System_Config_Source_Order_Status_Abstract
{
    protected function collectStatuses()
    {
        $result = array();

        $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        foreach ($statuses as $code => $label) {
            $result[] = array(
               'value' => $code,
               'label' => $label
            );
        }
        return $result;
    }
}
