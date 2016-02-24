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

    public function isLipscoreActive()
    {
        try {
            return $this->isLipscoreModuleEnabled() && $this->isLipscoreOutputEnabled();
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
            return false;
        }
    }

    public function isLipscoreModuleEnabled()
    {
        try {
            return $this->isModuleEnabled(self::MODULE_NAME) && $this->isLipscoreEnabledByConfig();
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
            return false;
        }
    }

    protected function isLipscoreOutputEnabled()
    {
        return $this->isModuleOutputEnabled(self::MODULE_NAME) && $this->isLipscoreEnabledByConfig();
    }

    protected function isLipscoreEnabledByConfig()
    {
        #return $this->_lipscoreConfig->enabled();
        return true;
    }
}
