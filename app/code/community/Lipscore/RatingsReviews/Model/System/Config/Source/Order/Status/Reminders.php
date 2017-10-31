<?php
class Lipscore_RatingsReviews_Model_System_Config_Source_Order_Status_Reminders extends
    Lipscore_RatingsReviews_Model_System_Config_Source_Order_Status_Abstract
{
    public function toOptionArray()
    {
        $options = array();

        try {
            $options = $this->collectStatuses();
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }

        return $options;
    }
}
