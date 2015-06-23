<?php

class Lipscore_RatingsReviews_Helper_Module extends Lipscore_RatingsReviews_Helper_Abstract
{
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
}
