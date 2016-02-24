<?php

class Lipscore_RatingsReviews_Helper_Reminder extends Lipscore_RatingsReviews_Helper_Abstract
{
    protected $productHelper  = null;
    protected $purchaseHelper = null;
    protected $couponHelper   = null;
    protected $localeHelper   = null;

    public function __construct()
    {
        $this->productHelper  = Mage::helper('lipscore_ratingsreviews/product');
        $this->purchaseHelper = Mage::helper('lipscore_ratingsreviews/purchase');
        $this->couponHelper   = Mage::helper('lipscore_ratingsreviews/coupon');
        $this->localeHelper   = Mage::helper('lipscore_ratingsreviews/locale');

        parent::__construct();
    }

    public function orderData(Mage_Sales_Model_Order $order)
    {
        $this->initConfig($order->getStoreId());
        return array(
            'purchase' => $this->_purchaseData($order),
            'products' => $this->_productsData($order)
        );
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
        foreach ($products as $product) {
            $productsData[] = $this->_productData($product);
        }

        return $productsData;
    }

    protected function _productData($product)
    {
        return $this->productHelper->getProductData($product);
    }

    protected function _purchaseData(Mage_Sales_Model_Order $order)
    {
        $coupon = $this->couponHelper->generateCoupon();
        $email  = $this->purchaseHelper->getEmail($order);
        $name   = $this->purchaseHelper->getName($order);
        $lang   = $this->localeHelper->getStoreLocale();

        return array(
            'buyer_email'      => $email,
            'buyer_name'       => $name,
            'discount_descr'   => $coupon ? $this->couponHelper->getCouponDescription() : '',
            'discount_voucher' => $coupon ? $coupon->getCode() : '',
            'purchased_at'     => (int) $order->getCreatedAtDate()->get(),
            'lang'             => $lang
        );
    }

    public function initConfig($storeId)
    {
        $config = Mage::helper('lipscore_ratingsreviews/config')->getScoped(null, $storeId);
        $this->setLipscoreConfig($config);
        $this->updateHelpersConfig();
    }

    protected function updateHelpersConfig()
    {
        $this->productHelper->setLipscoreConfig($this->_lipscoreConfig);
        $this->purchaseHelper->setLipscoreConfig($this->_lipscoreConfig);
        $this->couponHelper->setLipscoreConfig($this->_lipscoreConfig);
        $this->localeHelper->setLipscoreConfig($this->_lipscoreConfig);
    }
}
