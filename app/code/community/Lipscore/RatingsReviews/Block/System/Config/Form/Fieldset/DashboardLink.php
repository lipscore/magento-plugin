<?php

class Lipscore_RatingsReviews_Block_System_Config_Form_Fieldset_DashboardLink extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return 'Advanced settings are available on <a href="https://members.lipscore.com/">your dashboard</a>';
    }
}
