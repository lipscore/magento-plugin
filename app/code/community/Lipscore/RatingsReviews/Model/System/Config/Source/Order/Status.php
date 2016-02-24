<?php
class Lipscore_RatingsReviews_Model_System_Config_Source_Order_Status
{
    public function toOptionArray()
    {
        $options = array(
            array('value' =>'', 'label' => Mage::helper('adminhtml')->__('--Please Select--'))
        );

        try {
            $options = $this->collectStatuses($options);
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }

        return $options;
    }

    protected function collectStatuses($options)
    {
        $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        foreach ($statuses as $code => $label) {
            $options[] = array(
               'value' => $code,
               'label' => $label
            );
        }
        return $options;
    }
}
