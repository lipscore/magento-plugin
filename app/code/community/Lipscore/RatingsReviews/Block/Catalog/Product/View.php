<?php

class Lipscore_RatingsReviews_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{
    /**
     * Get product reviews summary
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $templateType
     * @param bool $displayIfNoReviews
     * @return string
     */
    public function getReviewsSummaryHtml(Mage_Catalog_Model_Product $product, $templateType = false,
        $displayIfNoReviews = false)
    {
        return parent::getReviewsSummaryHtml($product, 'long', $displayIfNoReviews);
    }    
}
