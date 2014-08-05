<?php

class Lipscore_RatingsReviews_Block_System_Config_Form_Coupons extends Mage_Adminhtml_Block_System_Config_Form
{
    protected function _canShowField($field)
    {
        $generateCoupons = Mage::helper('lipscore_ratingsreviews/coupon')->isAutoGenerationSupported();
        
        switch ($field->getName()) {
            case 'coupons':
                return $generateCoupons;
                break;
            case 'coupons_warning':
                return !$generateCoupons;
                break;
            default:
                return parent::_canShowField($field);
                break;
        }
    }
}
