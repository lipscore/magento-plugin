<?php

class Lipscore_RatingsReviews_Block_Review_Helper extends Mage_Review_Block_Helper
{
    protected $_template = 'lipscore/rating/view.phtml';
    protected static $_availableTypes = array(
        'long'  => 'id="lipscore-rating"',
        'short' => 'class="lipscore-rating-small"'
    );
    
    public function getSummaryHtml($product, $templateType, $displayIfNoReviews)
    {
        empty(self::$_availableTypes[$templateType]) and $templateType = 'short';
    
        $this->setRatingType(self::$_availableTypes[$templateType]);
        $this->setProduct($product);
        
        try {
            $this->setWidgetAttrs($product, $templateType);
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }            
    
        return $this->toHtml();
    }
    
    protected function setWidgetAttrs($product, $templateType)
    {
        $widgetHelper  = $this->helper('lipscore_ratingsreviews/widget');
        $productHelper = $this->helper('lipscore_ratingsreviews/product');
    
        $productData  = $productHelper->getProductData($product);
        $productAttrs = $widgetHelper->getProductAttrs($productData);
        $this->setLsProductAttrs($productAttrs);
    
        if ($templateType == 'long') {
            $rsProductData = $productHelper->getRichsnippetProductData($product);
            $rsAttrs       = $widgetHelper->getRichsnippetPproductAttrs($rsProductData);
            $this->setRichsnippetAttrs($rsAttrs);
        }
    }
}
