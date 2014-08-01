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
        
        $idType    = $this->_lipscoreConfig->get('type', 'identifier');
        $idAttr    = $this->_lipscoreConfig->get('attr', 'identifier');
        $brandAttr = $this->_lipscoreConfig->get('attr', 'brand');

        return array(
            'name'   => $product->getName(),
            'brand'  => $brandAttr ? $this->_getAttributeValue($product, $brandAttr) : null,
            'idType' => $idType,
            'id'     => $idAttr ? $this->_getAttributeValue($product, $idAttr) : null,
            'url'    => $product->getProductUrl()
        );
    }
    
    protected function _getAttributeValue(Mage_Catalog_Model_Product $product, $attrCode)
    {
        $attr = $product->getResource()->getAttribute($attrCode);
        
        if ('select' == $attr->getFrontendInput()) {
            return $attr->getSource()->getOptionText($product->getData($attrCode));
        } else {
            return $product->getData($attrCode);
        }
    }
}
