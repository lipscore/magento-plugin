<?php

class Lipscore_RatingsReviews_Block_System_Config_Form_Fieldset_Coupons extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $warning = '<span class="critical">Warning!</span> ';
        $message = 'This feature is unavailable: current Magento version doesn\'t support coupons auto generation.';
        return '<div class="notification-global"><strong>' . $warning . $message . '</strong></div>';
    }
}
