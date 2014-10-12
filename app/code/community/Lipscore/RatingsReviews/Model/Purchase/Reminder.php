<?php

class Lipscore_RatingsReviews_Model_Purchase_Reminder
{
    protected $_productHelper = null;
    
    const LOG_FILE = 'lipscore_reminder.log';
    
    public function __construct()
    {
        $this->_productHelper = Mage::helper('lipscore_ratingsreviews/product');        
    }
    
    public function send($orders)
    {
        $data = array();
        foreach ($orders as $order) {
            $data[] = array(
                'purchase' => $this->_purchaseData($order),
                'products' => $this->_productsData($order)
            );
        }
        
        return $this->_sendRequest(array('purchases' => $data));
    }
    
    protected function _productsData($order)
    {
        $orderItems = $order->getAllVisibleItems();
        $productIds = array();
        foreach ($orderItems as $item) {
            $productIds[] = $item->getProductId();
        }
        
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('entity_id', array('in' => $productIds))
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes());
        
        $productsData = array();
        $key = 1;
        foreach ($products as $product) {
            // we use keys to force json encoding of array to object (php 5.2 doesn't support JSON_FORCE_OBJECT)
            $productsData[$key++] = $this->_productData($product);
        }
        
        return $productsData;
    }
    
    protected function _productData($product)
    {
        $data = $this->_productHelper->getProductData($product);
        return array(
            'name'     => $data['name'], 
            'brand'    => $data['brand'], 
            'id_type'  => $data['idType'], 
            'id_value' => $data['id'], 
            'url'      => $data['url'], 
        );
    }
    
    protected function _purchaseData($order)
    {
        $couponHelper = Mage::helper('lipscore_ratingsreviews/coupon');
        $coupon = $couponHelper->generateCoupon();
        $email  = Mage::helper('lipscore_ratingsreviews/purchase')->getEmail($order);        
        
        return array(
            'buyer_email'      => $email,
            'discount_descr'   => $coupon ? $couponHelper->getCouponDescription() : '',
            'discount_voucher' => $coupon ? $coupon->getCode() : '',
            'purchased_at'     => $order->getCreatedAtDate()->get()
        );        
    }
    
    protected function _sendRequest($data)
    {
        $apiKey = Mage::getModel('lipscore_ratingsreviews/config')->apiKey();
        $apiUrl = Mage::getModel('lipscore_ratingsreviews/config_env')->apiUrl();
        
        $client = new Varien_Http_Client('http://api.lip-stripe.ngrok.com/purchases?api_key=' . $apiKey);
        $client->setRawData(json_encode($data), 'application/json')
               ->setMethod(Zend_Http_Client::POST);
        
        $response = $client->request();
        $result   = $response->isSuccessful();
                    
        self::_log($data, $result, $response->getRawBody());            
        return $result ? count(json_decode($response->getRawBody())) : 0;
    }
    
    protected static function _log($data, $isSuccessful, $msg)
    {
        $result = $isSuccessful ? 'Reminders were created: ' : 'Reminders weren\'t created: ';
        Mage::log($result . $msg, Zend_Log::INFO, self::LOG_FILE);
    }
}
