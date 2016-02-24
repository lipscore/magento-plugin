<?php

class Lipscore_RatingsReviews_Model_Observer_Module extends Lipscore_RatingsReviews_Model_Observer_Abstract
{
    public function checkVersion(Varien_Event_Observer $observer)
    {
        try {
            $this->_checkVersion($observer);
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }
    }

    private function _checkVersion(Varien_Event_Observer $observer)
    {
        if ($this->moduleHelper->isNewVersion()) {
            $website = Mage::app()->getWebsite();
            $tracker = Mage::getModel('lipscore_ratingsreviews/tracker_installation');
            $tracker->trackUpgrade($website);
        }
    }
}
