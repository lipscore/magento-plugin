<?php
class Lipscore_RatingsReviews_Block_System_Config_Form_Field_Reminderperiod
      extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public static $_periodSelect = 'reminder_period';
    
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->getPeriodLabel() . $this->getPeriodSelect() . $this->getReminderButton(); 
    }
    
    public function getPeriodSelect()
    {
        $opts = Mage::getSingleton('lipscore_ratingsreviews/system_config_source_reminderperiod')->toOptionArray();
        
        $select = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setName(self::$_periodSelect)
            ->setId(self::$_periodSelect)
            ->setOptions($opts);
        
        return $select->getHtml();
    }
    
    public function getPeriodLabel()
    {
        return '<label for="' . self::$_periodSelect . '">' . $this->__('Period') . '</label>';
    }
    
    public function getReminderButton()
    {
        $url           = $this->getUrl('/purchases_reminders/send');
        $perioSelectId = self::$_periodSelect;
        
        $apiKey = Mage::getModel('lipscore_ratingsreviews/config')->apiKey();
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => $this->__('Send Reminders'),                
                'disabled'  => empty($apiKey),
                'title'     => $this->__('Send Reminders'),
                'onclick'   => "sendLipscoreReminder('$url', '$perioSelectId');",
            ));
        return $button->toHtml();             
    }
}
