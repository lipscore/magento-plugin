<?php

class Lipscore_RatingsReviews_Model_Observer
{
    const REVIEW_MODULE = 'Mage_Review';
    const RATING_MODULE = 'Mage_Rating';

    public function __call($method, $arguments) {
        try {
            call_user_func_array(array($this, $method), $arguments);
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }
    }
    
    protected function manageMageReviewModule(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('core/data')->isModuleEnabled(self::REVIEW_MODULE)) {
            $nodePath = 'modules/' . self::REVIEW_MODULE . '/active';
            Mage::getConfig()->setNode($nodePath, 'true', true);
        }
        
        $this->_disableModuleOutput(self::REVIEW_MODULE);
        $this->_disableModuleOutput(self::RATING_MODULE);
    }
    
    protected function addRatings(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        if ($collection->count()) {
            foreach ($collection as $product) {
                $product->setData('rating_summary', 1);
            }
        }

        return $this;
    }
    
    protected function beforeLoadProductPage(Varien_Event_Observer $observer)
    {
        $layout = $observer->getEvent()->getLayout();
        $tabs = $layout->getBlock('product.info.tabs');
        if ($tabs) {
            $tabs->addTab('lipscore.reviews', 'Reviews', 'core/template', 'lipscore/reviews/view.phtml');
        }   
    }

    protected function _disableModuleOutput($moduleName)
    {
        $outputPath = 'advanced/modules_disable_output/' . $moduleName;
        if (!Mage::getStoreConfig($outputPath)) {
            Mage::app()->getStore()->setConfig($outputPath, true);
        }
    }
}
