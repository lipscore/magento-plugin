<?php

class Lipscore_RatingsReviews_Model_Observer_Output extends Lipscore_RatingsReviews_Model_Observer_Abstract
{
    const REVIEW_MODULE = 'Mage_Review';
    const RATING_MODULE = 'Mage_Rating';

    const REVIEW_TITLE_PLACEHOLDER = 'lipscore_reviews_placeholder';

    protected function manageMageReviewModule(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('core/data')->isModuleEnabled(self::REVIEW_MODULE)) {
            $nodePath = 'modules/' . self::REVIEW_MODULE . '/active';
            Mage::getConfig()->setNode($nodePath, 'true', true);
        }

        $this->disableModuleOutput(self::REVIEW_MODULE);
        $this->disableModuleOutput(self::RATING_MODULE);

        $nodePath = 'global/blocks/review/rewrite/helper';
        Mage::getConfig()->setNode($nodePath, 'Lipscore_RatingsReviews_Block_Review_Helper', true);

        $nodePath = 'global/blocks/catalog/rewrite/product_view';
        Mage::getConfig()->setNode($nodePath, 'Lipscore_RatingsReviews_Block_Catalog_Product_View', true);
    }

    protected function addRatings(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        if ($collection->count()) {
            foreach ($collection as $product) {
                if (!is_a($product->getRatingSummary(), 'Mage_Review_Model_Review_Summary')) {
                    Mage::getModel('review/review')->getEntitySummary($product);
                }
            }
        }

        return $this;
    }

    protected function addReviewsTab(Varien_Event_Observer $observer)
    {
        $layout = $observer->getEvent()->getLayout();

        if (!$layout) {
            return;
        }

        $tabs = $layout->getBlock('product.info.tabs');
        if ($tabs && method_exists($tabs, 'addTab')) {
            $tabs->addTab(
                'lipscore_reviews', self::REVIEW_TITLE_PLACEHOLDER, 'lipscore_ratingsreviews/catalog_product_reviews',
                'lipscore/reviews/view.phtml'
            );
        }
    }

    protected function addReviewsFeatures(Varien_Event_Observer $observer)
    {
        $block  = $observer->getBlock();
        $layout = $block->getLayout();

        if (!$layout) {
            return;
        }

        $layoutHandles = $layout->getUpdate()->getHandles();
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
                $reviewsBlock = $block->getLayout()->createBlock('lipscore_ratingsreviews/review_single');
                $html .= $reviewsBlock->toHtml();
            }

            $transport->setHtml($html);
        }
    }

    protected function methodAvailable($method)
    {
        return $this->moduleHelper->isLipscoreActive();
    }
}
