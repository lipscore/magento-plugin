<?php
class Lipscore_RatingsReviews_Block_System_Config_Form_Field_Emailstext
      extends Lipscore_RatingsReviews_Block_System_Config_Form_Field_Abstract
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return '<p>The single most important feature to get ratings and reviews is to send existing customers Review
            Request Emails after the customer has received the product.<br/>
            Please choose which order status that triggers these emails (previews can be seen in your
            <a href="https://members.lipscore.com/">Lipscore Dashboard</a>)</p>';
    }
}
