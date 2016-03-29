<?php

class Lipscore_RatingsReviews_Helper_Coupon extends Lipscore_RatingsReviews_Helper_Abstract
{
    protected $_rule;
    protected $_ruleModel;

    public function __construct()
    {
        parent::__construct();
        $this->_ruleModel = Mage::getModel('salesrule/rule');
    }

    function generateCoupon()
    {
        $coupon = null;

        $rule = $this->getRule();
        if ($rule) {
            $params = array(
                'rule_id' => $rule->getId(),
                'qty'     => 1,
                'length'  => $this->_lipscoreConfig->get('length', 'coupon'),
                'format'  => $this->_lipscoreConfig->get('format', 'coupon'),
                'prefix'  => $this->_lipscoreConfig->get('prefix', 'coupon'),
                'suffix'  => $this->_lipscoreConfig->get('suffix', 'coupon'),
                'dash'    => $this->_lipscoreConfig->get('dash',   'coupon'),
            );

            $generator = Mage::getModel('lipscore_ratingsreviews/coupon_generator');
            $coupon = $generator->generate($rule, $params);
        }

        return $coupon;
    }

    public function getRule()
    {
        $ruleId = $this->_lipscoreConfig->get('rule_id', 'coupon');
        if ($ruleId) {
            return $this->_ruleModel->load($ruleId);
        }
    }

    public function getCouponDescription()
    {
        $rule = $this->getRule();
        return $rule ? $rule->getDescription() : '';
    }

    public function isAutoGenerationSupported()
    {
        return method_exists($this->_ruleModel, 'getCouponMassGenerator');
    }
}
