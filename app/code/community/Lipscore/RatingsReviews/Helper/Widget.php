<?php

class Lipscore_RatingsReviews_Helper_Widget extends Lipscore_RatingsReviews_Helper_Abstract
{
    public function getProductAttrs($productData)
    {
        $attrs = '';
        try {
            $attrs = $this->_getProductAttrs($productData);
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }
        return $attrs;
    }
    
    public function getRichsnippetPproductAttrs($productData)
    {
        $attrs = '';
        try {
            $attrs = $this->_getRichsnippetPproductAttrs($productData);
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }        
        return $attrs;
    }
    
    protected function _getProductAttrs($productData)
    {
        $attrs = array(
            'ls-product-name'   => $productData['name'],
            'ls-brand'          => $productData['brand'],
            'ls-sku'            => implode(';', $productData['sku_values']),
            'ls-product-id'     => $productData['internal_id'],
            'ls-image-url'      => $productData['image_url'],
            'ls-price'          => $productData['price'],
            'ls-price-currency' => $productData['currency'],
            'ls-category'       => $productData['category']
        );
        return $this->_toString($attrs);
    }
    
    protected function _getRichsnippetPproductAttrs($productData)
    {
        $attrs = array(
            'ls-description'  => $productData['description'],
            'ls-avaialbility' => $productData['availability']
        );
        return $this->_toString($attrs);
    }
    
    protected function _toString($attrs)
    {
        $strAttrs = array();
        foreach ($attrs as $attr => $value) {
            $value = htmlspecialchars($value);
            $strAttrs[] = "$attr=\"$value\"";
        }        
        return implode($strAttrs, ' ');
    }
}
