<?php

class Lipscore_RatingsReviews_Model_Observer
{
    const REVIEW_MODULE = 'Mage_Review';
    const RATING_MODULE = 'Mage_Rating';
    
    const REVIEW_TITLE_PLACEHOLDER = 'lipscore_reviews_placeholder';

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
    
    protected function addReviewsTab(Varien_Event_Observer $observer)
    {
        $layout = $observer->getEvent()->getLayout();
        $tabs = $layout->getBlock('product.info.tabs');
        if ($tabs) {
            $tabs->addTab(
                'lipscore.reviews', self::REVIEW_TITLE_PLACEHOLDER, 'core/template', 'lipscore/reviews/view.phtml'
            );
        }   
    }
    
    public function addReviewsFeatures(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();
        
        $layoutHandles = $block->getLayout()->getUpdate()->getHandles();
        $properLayout  = in_array('catalog_product_view', $layoutHandles);
        $properBlock   = $block->getNameInLayout() == 'product.info';
        
        if ($properLayout && $properBlock) {
            $transport = $observer->getTransport();
            $html = $transport->getHtml();
            
            // set review title
            $titleBlock = $block->getLayout()->createBlock('lipscore_ratingsreviews/review_tabtitle');
            $html = str_replace(self::REVIEW_TITLE_PLACEHOLDER, $titleBlock->toHtml(), $html);
            
            // ensure that reviews block exists on a page
            $pos = strripos($html, 'lipscore-review-list');
            if ($pos === false) {
                $reviewsBlock = $block->getLayout()->createBlock('core/template', '', array('template' => 'lipscore/reviews/view.phtml'));
                $html .= $reviewsBlock->toHtml();
            }
            
            $transport->setHtml($html);
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
