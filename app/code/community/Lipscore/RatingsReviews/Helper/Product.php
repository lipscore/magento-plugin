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
            'brand'  => $brandAttr ? $product->getAttributeText($brandAttr) : null,
            'idType' => $idType,
            'id'     => $idAttr ? $product->getData($idAttr) : null,
            'url'    => $product->getProductUrl()
        );
    }
}
