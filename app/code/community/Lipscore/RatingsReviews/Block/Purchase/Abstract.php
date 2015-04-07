<?php

abstract class Lipscore_RatingsReviews_Block_Purchase_Abstract extends Mage_Core_Block_Template
{
    protected function _beforeToHtml()
    {
        try {
            $this->_prepareProductsInOrder();
            $this->_preparePurchaseInfo();
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }            

        return parent::_beforeToHtml();
    }
    
    protected abstract function _prepareProductsInOrder();
    protected abstract function _preparePurchaseInfo();

    protected function _prepareCouponInfo()
    {
        $couponHelper = $this->helper('lipscore_ratingsreviews/coupon');
        $coupon = $couponHelper->generateCoupon();

        if ($coupon) {
            $this->setCouponCode($coupon->getCode());
            $this->setCouponDescription($couponHelper->getCouponDescription());
        }
    }
}
