<?php

class Lipscore_RatingsReviews_CouponController extends Mage_Core_Controller_Front_Action
{
    public function generateAction()
    {
        
    }

    protected function _create($couponData)
    {
        $ruleId = $this->getRequest()->getParam('rule_id');
        $couponData['rule_id'] = $ruleId;
    
        $rule = $this->_loadSalesRule($ruleId);
        // Reference the MassGenerator on this rule.
        /** @var Mage_SalesRule_Model_Coupon_Massgenerator $generator */
        $generator = $rule->getCouponMassGenerator();
        // Validate the generator
        if (!$generator->validateData($couponData)) {
            $this->_critical(Mage::helper('salesrule')->__('Coupon AutoGen API: Invalid parameters passed in.'),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        } else {
            // Set the data for the generator
            $generator->setData($couponData);
            // Generate a pool of coupon codes for the Generate Coupons rule
            $generator->generatePool();
        }
    }
    
    /**
     * Retrieve list of coupon codes.
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $ruleId = $this->getRequest()->getParam('rule_id');
        $rule = $this->_loadSalesRule($ruleId);
        /** @var Mage_SalesRule_Model_Resource_Coupon_Collection $collection */
        $collection = Mage::getResourceModel('salesrule/coupon_collection');
        $collection->addRuleToFilter($rule);
    
        $this->_applyCollectionModifiers($collection);
    
        $data = $collection->load()->toArray();
        return $data['items'];
    }
    
    /**
     * Load sales rule by ID.
     *
     * @param int $ruleId
     * @return Mage_SalesRule_Model_Rule
     */
    protected function _loadSalesRule($ruleId)
    {
        if (!$ruleId) {
            $this->_critical(Mage::helper('salesrule')
                ->__('Rule ID not specified.'), Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
    
        $rule = Mage::getModel('salesrule/rule')->load($ruleId);
        if (!$rule->getId()) {
            $this->_critical(Mage::helper('salesrule')
                ->__('Rule was not found.'), Mage_Api2_Model_Server::HTTP_NOT_FOUND);
        }
    
        return $rule;
    }    
}
