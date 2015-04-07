<?php

class Lipscore_RatingsReviews_Block_Purchase_Onepage extends Lipscore_RatingsReviews_Block_Purchase_Abstract 
{
    protected $_order = null;
    
    protected function _construct()
    {
        try {
            $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
            if ($orderId) {
                $this->_order = Mage::getModel('sales/order')->load($orderId);
            }
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }        
        
        parent::_construct();
    }
    
    protected function _prepareProductsInOrder()
    {
        $productsData = array();
        
        if ($this->_order) {
            $productHelper = $this->helper('lipscore_ratingsreviews/product');
            
            $orderItems = $this->_order->getAllVisibleItems();
            foreach ($orderItems as $item) {
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                $productsData[] = $productHelper->getProductData($product);
            }
        }
        
        $this->setProductsData($productsData);        
    }
    
    protected function _preparePurchaseInfo()
    {
        if (!$this->_order) {
            return;
        }

        $purchaseHelper = $this->helper('lipscore_ratingsreviews/purchase');
        
        $this->setCustomerEmail($purchaseHelper->getEmail($this->_order));
        $this->setCustomerName($purchaseHelper->getName($this->_order));

        // coupon
        $this->_prepareCouponInfo();
    }
}
