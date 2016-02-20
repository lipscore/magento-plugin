<?php

/**
* Product helper
*
* @author oivanova
*/

class Lipscore_RatingsReviews_Helper_Product extends Lipscore_RatingsReviews_Helper_Abstract
{   
    public function getProductData(Mage_Catalog_Model_Product $product = null)
    {
        $data = array();
        try {
            $data = $this->_getProductData($product);
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }
        return $data;
    }
    
    public function getRichsnippetProductData(Mage_Catalog_Model_Product $product = null)
    {
        $data = array();
        try {
            $data = $this->_getRichsnippetProductData($product);
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }
        return $data;
    }
    
    protected function _getProductData(Mage_Catalog_Model_Product $product = null)
    {
        $product or $product = Mage::registry('product');
        
        $brandAttr = $this->_lipscoreConfig->brandAttr();
        
        return array(
            'name'         => $product->getName(),
            'brand'        => $this->getAttributeValue($product, $brandAttr),
            'sku_values'   => array($this->getSku($product)),
            'internal_id'  => "{$product->getId()}",
            'url'          => $product->getProductUrl(),
            'image_url'    => $this->getImageUrl($product),
            'price'        => $this->getPrice($product),
            'currency'     => $this->getCurrency(),
            'category'     => $this->getCategory($product)
        );        
    }

    public function _getRichsnippetProductData(Mage_Catalog_Model_Product $product = null)
    {
        $product or $product = Mage::registry('product');
        
        return array(
            'description'  => $this->getDescription($product),
            'availability' => $this->getAvailability($product)
        );        
    }
    
    protected function getImageUrl(Mage_Catalog_Model_Product $product)
    {
        $url = '';
        try {
            $url = (string) Mage::helper('catalog/image')->init($product, 'image');
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $url;
    }
    
    protected function getCategory($product)
    {
        $category = Mage::registry('current_category');
        if (!$category) {
            $categoryIds = $product->getCategoryIds();
            if (isset($categoryIds[0])) {
                $category = Mage::getModel('catalog/category')->load($categoryIds[0]);
            }
        }
        return $category ? $category->getName() : '';
    }
    
    protected function getAvailability(Mage_Catalog_Model_Product $product)
    {
        $isAvailable = $product->isAvailable();
        if ($product->isGrouped()) {
            $associated  = $product->getTypeInstance(true)->getAssociatedProducts($product);
            $isAvailable = $isAvailable && count($associated);
        }
        return (int) $isAvailable;        
    }
    
    protected function getDescription(Mage_Catalog_Model_Product $product)
    {
        $description = $product->getShortDescription();
        if (!$description) {
            $description = $product->getDescription();
        }
        return $description;
    }
    
    protected function getPrice($product)
    {
        return $this->priceHelper()->getProductPrice($product);
    }
    
    protected function getCurrency()
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }
    
    protected function getSku($product)
    {
        $sku = $product->getSku();
        if (!$sku) {
            $sku = Mage::getModel('catalog/product')->load($product->getId())->getSku();
        }
        return $sku;    
    }
    
    protected function getAttributeValue(Mage_Catalog_Model_Product $product, $attrCode)
    {
        $attr = $product->getResource()->getAttribute($attrCode);
        
        if (!$attr) {            
            return null;
        }
        
        if ('select' == $attr->getFrontendInput()) {
            return $attr->getSource()->getOptionText($product->getData($attrCode));
        } else {
            return $product->getData($attrCode);
        }
    }
    
    protected function priceHelper()
    {
        if (!isset($this->priceHelper)) {
            $this->priceHelper = Mage::helper('lipscore_ratingsreviews/price');
        }
        return $this->priceHelper;
    }    
}
