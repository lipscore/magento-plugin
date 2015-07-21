<?php

class Lipscore_RatingsReviews_Helper_Price extends Lipscore_RatingsReviews_Helper_Abstract
{
    public function getProductPrice(Mage_Catalog_Model_Product $product)
    {
        $price = 0;        
        try {
            $price = $this->_getProductPrice($product);
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }        
        return $price;
    }
    
    protected function _getProductPrice(Mage_Catalog_Model_Product $product)
    {
        $price = 0;
        
        if ($this->isMsrpAppliable($product)) {
            return $price;
        }
        
        if ($this->isBundleProduct($product)) {
            $price = $this->getBundlePrice($product);
        } elseif ($product->isGrouped()) {
            $price = $this->getGroupedPrice($product);
        } else {
            $price = $this->getSimplePrice($product);
        }
        
        return round($price, 2);        
    }
    
    protected function isBundleProduct(Mage_Catalog_Model_Product $product)
    {
        return $product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE;
    }
    
    protected function getBundlePrice(Mage_Catalog_Model_Product $product)
    {
        $price      = 0;
        $priceModel = $product->getPriceModel();
        $taxHelper  = $this->taxHelper();
    
        $options = $product->getTypeInstance(true)->getOptions($product);
        foreach ($options as $option) {
            $selectionProduct = $option->getDefaultSelection();
            if ($selectionProduct) {
                $qty = $selectionProduct->getSelectionQty();
                $val = $priceModel->getSelectionPreFinalPrice($product, $selectionProduct, $qty);
                $price += $taxHelper->getPrice($selectionProduct, $val, true);
            }
        }
    
        if (!$price) {
            if (method_exists($priceModel, 'getTotalPrices')) {
                $price = $priceModel->getTotalPrices($product, 'min', true);
            } else {
                // deprecated after 1.5.1.0
                $price = $priceModel->getPricesDependingOnTax($product, 'min', true);
            }
        }
    
        return $price;
    }
    
    protected function getGroupedPrice(Mage_Catalog_Model_Product $product)
    {
        $price = 0;
        $taxHelper = $this->taxHelper();
    
        $associated = $product->getTypeInstance(true)->getAssociatedProducts($product);
        foreach ($associated as $product) {
            $price += $taxHelper->getPrice($product, $product->getFinalPrice(), true);
        }
    
        return $price;
    }
    
    protected function getSimplePrice(Mage_Catalog_Model_Product $product)
    {
        return $this->taxHelper()->getPrice($product, $product->getFinalPrice(), true);
    }
    
    protected function isMsrpAppliable(Mage_Catalog_Model_Product $product)
    {
        $catalogHelper = Mage::helper('catalog');
        if (method_exists($catalogHelper, 'canApplyMsrp')) {
            return (int) $catalogHelper->canApplyMsrp($product, null, false);
        }
        return 0;
    }
    
    protected function taxHelper()
    {
        if (!isset($this->taxHelper)) {
            $this->taxHelper = Mage::helper('tax');
        }
        return $this->taxHelper;
    }    
}
