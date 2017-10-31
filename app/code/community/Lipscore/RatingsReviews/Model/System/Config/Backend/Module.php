<?php

class Lipscore_RatingsReviews_Model_System_Config_Backend_Module extends Mage_Core_Model_Config_Data
{
    protected $isChanged = false;

    protected function _afterSave()
    {
        parent::_afterSave();

        $this->isChanged = true;

        return $this;
    }

    public function afterCommitCallback()
    {
        parent::afterCommitCallback();

        try {
            $this->flushCache();
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }

        return $this;
    }

    protected function flushCache()
    {
        if ($this->isChanged) {
            Mage::app()->cleanCache();
        }
    }
}
