<?php
class Lipscore_RatingsReviews_Block_System_Config_Form_Field_Reminderperiod
      extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public static $_periodSelect = 'reminder_period';
    
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->getNote() . $this->getPeriodLabel() . $this->getPeriodSelect() . $this->getReminderButton(); 
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
    
    public function getNote()
    {
        $linkToDasboard = 'https://members.lipscore.com/';
        $linkToCoupons  = $this->getUrl('*/*/*', array('section' => 'lipscore_coupons'));
                  
        $msg  = "After installation of Lipscore you can send emails to recent customers asking them to write reviews of the purchases they have done. This is done automatically for all future customers but customers from before Lipscore was installed will not get these emails unless you invoke it below. They will be delivered according to <a href='$linkToDasboard'>reminder delay settings</a> and will only be done once.";
        $coupons = "To increase the chance of getting reviews you can also add coupons to these emails. Set up coupons <a href='$linkToCoupons'>here</a>.";
        
        return "<p>$msg $coupons<p>";
    }
}
