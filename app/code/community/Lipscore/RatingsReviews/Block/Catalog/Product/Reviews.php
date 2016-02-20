<?php

class Lipscore_RatingsReviews_Block_Catalog_Product_Reviews extends Mage_Catalog_Block_Product_View
{
    protected function _beforeToHtml()
    {
        try {
            $this->prepareWidgetAttrs();
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }

        return parent::_beforeToHtml();
    }

    protected function prepareWidgetAttrs()
    {
        $widgetHelper  = $this->helper('lipscore_ratingsreviews/widget');
        $productHelper = $this->helper('lipscore_ratingsreviews/product');

        $productData  = $productHelper->getProductData($this->getProduct());
        $productAttrs = $widgetHelper->getProductAttrs($productData);

        $this->setLsProductAttrs($productAttrs);
    }
}
