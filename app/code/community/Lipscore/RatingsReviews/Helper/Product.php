<?php

/**
* Product helper
*
* @author oivanova
*/

class Lipscore_RatingsReviews_Helper_Product extends Lipscore_RatingsReviews_Helper_Abstract
{    
    function getProductData(Mage_Catalog_Model_Product $product = null)
    {
        $product or $product = Mage::registry('product');
        
        $idType    = $this->_lipscoreConfig->identifierType();
        $idAttr    = $this->_lipscoreConfig->identifierAttr();
        $brandAttr = $this->_lipscoreConfig->brandAttr();

        return array(
            'name'   => $product->getName(),
            'brand'  => $this->_getAttributeValue($product, $brandAttr),
            'idType' => $idType,
            'id'     => $this->_getAttributeValue($product, $idAttr),
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
