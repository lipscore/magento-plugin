<?php

class Lipscore_RatingsReviews_Block_Catalog_Product_View_Richsnippet extends Mage_Catalog_Block_Product_View
{
    const RS_IMG_SIZE = 120;
    
    protected function _beforeToHtml()
    {
        try {
            $this->prepareProductData();
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }
    
        return parent::_beforeToHtml();
    }
    
    protected function prepareProductData()
    {
        $product = $this->getProduct();
        
        $this->setRsName($product->getName());
        $this->setRsBrand($this->productHelper()->getBrand($product));
        $this->setRsDescription($product->getDescription());
        $this->setRsImg($this->getImgUrl());
        $this->setRsProductId($this->getProductId());
        $this->setRsPrice($this->getPrice());
        $this->setRsCurrency($this->getCurrentCurrency());
        $this->setRsAvailability($this->getAvailability());
    }
    
    protected function getImgUrl()
    {
        $img = Mage::helper('catalog/image')->init($this->getProduct(), 'small_image');
        return $img->resize(self::RS_IMG_SIZE, self::RS_IMG_SIZE);
    }
    
    protected function getProductId()
    {
        $idType = $this->productHelper()->getIdType();
        $id     = $this->productHelper()->getIdentifier($this->getProduct());
        return "$idType:$id";
    }
    
    protected function getPrice()
    {
        $product = $this->getProduct();
        $price   = 0;
        
        if ($this->isMsrpAppliable()) {
            return $price;
        }
        
        if ($this->isBundleProduct()) {
            $price = $this->getBundlePrice();            
        } elseif ($this->getProduct()->isGrouped()) {
            $price = $this->getGroupedPrice();
        } else {
            $price = $this->getSimplePrice();
        }        
        
        return round($price, 2);
    }
    
    protected function getCurrentCurrency()
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }
    
    protected function getAvailability()
    {
        $product = $this->getProduct();
        $isAvailable = $product->isAvailable();
        if ($product->isGrouped()) {
            $associated  = $product->getTypeInstance(true)->getAssociatedProducts($product);
            $isAvailable = $isAvailable && count($associated);
        }
        return $isAvailable ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock';
    }
    
    protected function productHelper()
    {
        if (!isset($this->productHelper)) {
            $this->productHelper = $this->helper('lipscore_ratingsreviews/product');
        }
        return $this->productHelper;
    }
    
    protected function isBundleProduct()
    {
        return $this->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE;
    }
    
    protected function getBundlePrice()
    {
        $price      = 0;
        $product    = $this->getProduct();
        $priceModel = $product->getPriceModel();
        $taxHelper  = $this->helper('tax');
        
        $options    = $product->getTypeInstance(true)->getOptions($product);
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
    
    protected function getGroupedPrice()
    {
        $price = 0;
        $taxHelper = $this->helper('tax');
                
        $associated = $this->getProduct()->getTypeInstance(true)->getAssociatedProducts($this->getProduct());
        foreach ($associated as $product) {
            $price += $taxHelper->getPrice($product, $product->getFinalPrice(), true);        
        }        
        
        return $price;
    }
    
    protected function getSimplePrice()
    {
        return $this->helper('tax')->getPrice($this->getProduct(), $this->getProduct()->getFinalPrice(), true);
    }
    
    protected function isMsrpAppliable()
    {
        $catalogHelper = $this->helper('catalog');
        if (method_exists($catalogHelper, 'canApplyMsrp')) {
            return (int) $catalogHelper->canApplyMsrp($this->getProduct(), null, false);
        }
        return 0;
    }
}
