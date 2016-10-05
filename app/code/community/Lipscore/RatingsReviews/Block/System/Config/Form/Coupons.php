<?php

class Lipscore_RatingsReviews_Block_System_Config_Form_Coupons extends Mage_Adminhtml_Block_System_Config_Form
{
    protected function _canShowField($field)
    {
        $generateCoupons = false;
        try {
            $generateCoupons = Mage::helper('lipscore_ratingsreviews/coupon')->isAutoGenerationSupported();
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }

        switch ($field->getName()) {
            case 'coupons':
                return (int) $generateCoupons;
                break;
            case 'coupons_warning':
                return (int) !$generateCoupons;
                break;
            default:
                return parent::_canShowField($field);
                break;
        }
    }
}
