<?php

class Lipscore_RatingsReviews_Block_Purchase_Onepage extends Lipscore_RatingsReviews_Block_Purchase_Abstract 
{
    protected $_order = null;
    
    protected function _construct()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        if ($orderId) {
            $this->_order = Mage::getModel('sales/order')->load($orderId);
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
                $productsData[] = $productHelper->getProductData($item->getProduct());
            }
        }
        
        $this->setProductsData($productsData);        
    }
    
    protected function _preparePurchaseInfo()
    {
        if (!$this->_order) {
            return;
        }

        // email
        if ($this->_order->getBillingAddress()->getEmail()) {
            $email = $this->_order->getBillingAddress()->getEmail();
        } else {
            $email = $this->_order->getCustomerEmail();
        }
        
        $this->setCustomerEmail($email);

        // coupon
        $this->_prepareCouponInfo();
    }
}
