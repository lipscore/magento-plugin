<?php

class Lipscore_RatingsReviews_Helper_Module extends Lipscore_RatingsReviews_Helper_Abstract
{
    const MODULE_NAME = 'Lipscore_RatingsReviews';
    
    public function getVersion()
    {
        return (string) Mage::getConfig()->getNode('modules/Lipscore_RatingsReviews/version');
    }
    
    public function isNewVersion()
    {
        $website        = Mage::app()->getWebsite();
        $lipscoreConfig = Mage::getModel('lipscore_ratingsreviews/config', array('website' => $website));
        $oldVersion     = (string) $lipscoreConfig->lastTrackedVersion();
        $newVersion     = $this->getVersion();
        
        return strcmp($oldVersion, $newVersion) < 0;
    }
    
    public function isActive()
    {
        return $this->isModuleEnabled() && $this->isOutputEnabled() && $this->isEnabledByConfig();
    }
    
    public function isModuleEnabled()
    {
        return Mage::helper('core/data')->isModuleEnabled(self::MODULE_NAME) && $this->isEnabledByConfig();
    }
    
    public function isOutputEnabled()
    {
        return Mage::helper('core')->isModuleOutputEnabled(self::MODULE_NAME) && $this->isEnabledByConfig();
    }
    
    public function isEnabledByConfig()
    {
        #return $this->_lipscoreConfig->enabled();
        return true;
    }
}
