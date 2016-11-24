<?php

class Lipscore_RatingsReviews_Block_System_Config_Form_Fieldset_Coupons extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $generateCoupons = false;
        try {
            $generateCoupons = Mage::helper('lipscore_ratingsreviews/coupon')->isAutoGenerationSupported();
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }

        if ($generateCoupons) {
            return parent::render($element);
        } else {
            return $this->renderWarning();
        }
    }

    protected function renderWarning()
    {
        $warning = '<span class="critical">Warning!</span> ';
        $message = 'This feature is unavailable: current Magento version doesn\'t support coupons auto generation.';
        return '<div class="notification-global"><strong>' . $warning . $message . '</strong></div>';
    }
}
