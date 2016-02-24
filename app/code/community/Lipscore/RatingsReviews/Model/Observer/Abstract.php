<?php

abstract class Lipscore_RatingsReviews_Model_Observer_Abstract
{
    protected $moduleHelper;

    public function __construct()
    {
        $this->moduleHelper = Mage::helper('lipscore_ratingsreviews/module');
    }

    public function __call($method, $arguments) {
        if ($this->moduleHelper->isLipscoreModuleEnabled()) {
            call_user_func_array(array($this, $method), $arguments);
        }
    }
}
