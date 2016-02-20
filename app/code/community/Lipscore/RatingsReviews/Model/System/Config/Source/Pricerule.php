<?php

class Lipscore_RatingsReviews_Model_System_Config_Source_Pricerule
{
    public function toOptionArray()
    {
        $attrs = array(
            array('value' => '', 'label' => Mage::helper('adminhtml')->__('--Please Select--'))
        );
        
        try {
            $attrs = array_merge($attrs, $this->_findAttrs());
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }
        
        return $attrs;
    }
    
    protected function _findAttrs()
    {
        $attrs = array();
        
        if (!Mage::helper('lipscore_ratingsreviews/coupon')->isAutoGenerationSupported()) {
            // magento doesn't support auto generation
            return $attrs;
        }
        
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
