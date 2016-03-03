<?php

class Lipscore_RatingsReviews_Helper_Config extends Lipscore_RatingsReviews_Helper_Abstract
{
    public function getScoped($websiteCode, $storeCode)
    {
        $website = $websiteCode ? Mage::getModel('core/website')->load($websiteCode) : null;
        $store   = $storeCode ? Mage::getModel('core/store')->load($storeCode) : null;

        return Mage::getModel('lipscore_ratingsreviews/config', array('store' => $store, 'website' => $website));
    }
}
