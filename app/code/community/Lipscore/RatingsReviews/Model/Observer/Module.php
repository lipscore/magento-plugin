<?php

class Lipscore_RatingsReviews_Model_Observer_Module extends Lipscore_RatingsReviews_Model_Observer_Abstract
{
    const MODULE = 'Lipscore_RatingsReviews';

    protected function checkVersion(Varien_Event_Observer $observer)
    {
        if ($this->moduleHelper->isNewVersion()) {
            $website = Mage::app()->getWebsite();
            $tracker = Mage::getModel('lipscore_ratingsreviews/tracker_installation');
            $tracker->trackUpgrade($website);
        }
    }

    protected function disableOutput(Varien_Event_Observer $observer)
    {
        if (!$this->moduleHelper->isLipscoreOutputEnabled()) {
            $this->disableModuleOutput(self::MODULE);
        }
    }

    protected function methodAvailable($method)
    {
        if ($method == 'disableOutput') {
            return true;
        }
        return $this->moduleHelper->isLipscoreModuleEnabled();
    }
}
