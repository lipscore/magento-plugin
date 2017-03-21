<?php
class Lipscore_RatingsReviews_Block_System_Config_Form_Field_Abstract
      extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected $lipscoreConfig = null;

    protected function getLipscoreConfig()
    {
        if (!$this->lipscoreConfig) {
            $this->lipscoreConfig = Mage::helper('lipscore_ratingsreviews/config')->getScoped(
                $this->getWebsite(), $this->getStore()
            );
        }
        return $this->lipscoreConfig;
    }

    protected function getSection()
    {
        return $this->getRequest()->getParam('section', '');
    }

    protected function getWebsite()
    {
        return $this->getRequest()->getParam('website', '');
    }

    protected function getStore()
    {
        return $this->getRequest()->getParam('store', '');
    }
}
