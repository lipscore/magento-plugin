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
            $data = $this->_productData($product);
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }
        return $data;
    }
    
    public function getIdentifier($product)
    {
        $idAttr = $this->_lipscoreConfig->identifierAttr();
        return $this->_getAttributeValue($product, $idAttr);
    }
    
    public function getBrand($product)
    {
        $brandAttr = $this->_lipscoreConfig->brandAttr();
        return $this->_getAttributeValue($product, $brandAttr);
    }
    
    public function getIdType()
    {
        return $this->_lipscoreConfig->identifierType();
    }
    
    protected function _productData(Mage_Catalog_Model_Product $product = null)
    {
        $product or $product = Mage::registry('product');
        
        return array(
            'name'   => $product->getName(),
            'brand'  => $this->getBrand($product),
            'idType' => $this->getIdType(),
            'id'     => $this->getIdentifier($product),
            'url'    => $product->getProductUrl()
        );        
    }
    
    protected function _getAttributeValue(Mage_Catalog_Model_Product $product, $attrCode)
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
}
