<?php
class Lipscore_RatingsReviews_Block_System_Config_Form_Field_Apikey
      extends Lipscore_RatingsReviews_Block_System_Config_Form_Field_Abstract
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        
        try {            
            $apiKey     = $this->getLipscoreConfig()->apiKey();
            $demoApiKey = $this->getLipscoreConfig()->demoApiKey();
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
            return parent::render($element);
        }            
        $comment = ($apiKey == $demoApiKey) ? $this->_commentHtml() : '';
        
        return parent::render($element) . $comment;
    }

    protected function _commentHtml()
    {
        $comment = 'Your Lipscore installation is set up using a Demo Account. Please sign up with your own account on <a href="http://lipscore.com/" target="_blank">www.lipscore.com</a> to get access to all available features.';
        return "<tr><td colspan='4'><span class='lipscore-notice-msg'>$comment</span></td></tr>";
    }
}
