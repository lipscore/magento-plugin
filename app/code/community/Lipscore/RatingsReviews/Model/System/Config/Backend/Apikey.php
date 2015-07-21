<?php

class Lipscore_RatingsReviews_Model_System_Config_Backend_Apikey extends Mage_Core_Model_Config_Data
{
    protected $trackedWebsites = array();
     
    protected function _afterSave()
    {
        parent::_afterSave();
        
        if (!$this->isValueChanged()) {
            return $this;
        }    
        
        try {
            $this->findSitesForTracking();
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }
            
        return $this;
    }
    
    public function afterCommitCallback()
    {
        parent::afterCommitCallback();
        
        try {
            $this->trackChanges();
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }
            
        return $this;
    }
    
    protected function findSitesForTracking()
    {
        $storeCode = $this->getStoreCode();
        if (!empty($storeCode)) {
            return;
        }
        
        $websiteCode = $this->getWebsiteCode();
        
        if ($websiteCode) {
            $this->trackedWebsites[] = Mage::app()->getWebsite($websiteCode);
        } else {
            $path = $this->getPath();
            foreach (Mage::app()->getWebsites() as $website) {
                $inherit = false;
                Mage::getModel('adminhtml/config_data')
                    ->setSection('lipscore_general')
                    ->setWebsite($website->getCode())
                    ->getConfigDataValue($path, $inherit);
                if ($inherit) {
                    $this->trackedWebsites[] = $website;
                }
            }
        }        
    }
    
    protected function trackChanges()
    {
        Mage::app()->getConfig()->reinit();
        
        $tracker  = Mage::getModel('lipscore_ratingsreviews/tracker_action');        
        foreach ($this->trackedWebsites as $website) {
            $tracker->track(Lipscore_RatingsReviews_Model_Tracker_Action::API_KEY_UPDATED, $website);
        }        
    }
}
