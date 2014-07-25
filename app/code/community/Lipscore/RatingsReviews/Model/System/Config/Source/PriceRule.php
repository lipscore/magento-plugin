<?php

class Lipscore_RatingsReviews_Model_System_Config_Source_PriceRule
{
    public function toOptionArray()
    {
        $attrs = array(
            array('value' =>'', 'label' => Mage::helper('adminhtml')->__('--Please Select--'))
        );

        $collection = Mage::getModel('salesrule/rule')
            ->getResourceCollection()
            ->addFieldToFilter('coupon_type', Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC)
            ->addFieldToFilter('use_auto_generation', '1');

        if ($collection->getSize() > 0) {
            foreach ($collection->getItems() as $rule) {
                $attrs[] = array(
                    'value' => $rule->getRuleId(),
                    'label' => $rule->getName()
                );
            }
        }

        return $attrs;
    }
}
