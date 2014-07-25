<?php

class Lipscore_RatingsReviews_Block_Purchase_Multishipping extends Lipscore_RatingsReviews_Block_Purchase_Abstract 
{
    protected $_orders = array();
    
    protected function _construct()
    {
        $orderIds = Mage::getSingleton('checkout/type_multishipping')->getOrderIds();
        if ($orderIds) {            
            $this->_orders = Mage::getModel('sales/order')
                ->getCollection()
                ->addFieldToFilter('entity_id', array('in' => $orderIds));
        }
        
        parent::_construct();
    }
    
    protected function _prepareProductsInOrder()
    {
        $productsData = array();
        
        $productHelper = $this->helper('lipscore_ratingsreviews/product');
        
        foreach ($this->_orders as $order) {
            $orderItems = $order->getAllVisibleItems();
            foreach ($orderItems as $item) {
                $product = $item->getProduct();
                $productsData[$product->getid()] = $productHelper->getProductData($product);
            }
        }
        
        $this->setProductsData($productsData);        
    }
    
    protected function _preparePurchaseInfo()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($customer) {
            $this->setCustomerEmail($customer->getEmail());
        }
        
        // coupon
        $this->_prepareCouponInfo();
    }
}
