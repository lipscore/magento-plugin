<?php

class Lipscore_RatingsReviews_Model_Observer
{
    const REVIEW_MODULE = 'Mage_Review';
    
    public function disableMageReviewModule(Varien_Event_Observer $observer)
    {
        if (Mage::helper('core/data')->isModuleEnabled(self::REVIEW_MODULE)) {
            $nodePath = 'modules/' . self::REVIEW_MODULE . '/active';
            Mage::getConfig()->setNode($nodePath, 'false', true);
        }
        
        $outputPath = 'advanced/modules_disable_output/' . self::REVIEW_MODULE;
        if (!Mage::getStoreConfig($outputPath)) {
            Mage::app()->getStore()->setConfig($outputPath, true);
        }
        Mage::app()->getCache()->clean();
    }

    public function manageCouponsSection(Varien_Event_Observer $observer)
    {
        $config = $observer->getConfig();
        
        if (!defined('Mage_SalesRule_Model_Rule::COUPON_TYPE_AUTO')) {
            // magento doesn't support auto generation
            $config->setNode('sections/lipscore_coupons', null);
        }
    }
}
