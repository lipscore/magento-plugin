<?php

class Lipscore_RatingsReviews_Helper_Reminder extends Lipscore_RatingsReviews_Helper_Abstract
{
    protected static $complexProductTypes = array(
        Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
        Mage_Catalog_Model_Product_Type::TYPE_GROUPED,
        Mage_Catalog_Model_Product_Type::TYPE_BUNDLE
    );

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

    public function singleReminderData(Mage_Sales_Model_Order $order)
    {
        $data = $this->orderData($order);
        unset($data['purchase']['purchased_at']);
        return $data;
    }

    public function multipleReminderData(Mage_Sales_Model_Order $order)
    {
        return $this->orderData($order);
    }

    protected function orderData(Mage_Sales_Model_Order $order)
    {
        $this->initConfig($order->getStoreId());
        return array(
            'purchase' => $this->purchaseData($order),
            'products' => $this->productsData($order)
        );
    }

    protected function productsData($order)
    {
        $orderItems = $order->getAllVisibleItems();
        $productIds = array();
        foreach ($orderItems as $item) {
            $productIds[] = $this->getProductIdFromOrderItem($item);
        }

        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('entity_id', array('in' => $productIds))
            ->addAttributeToSelect('*');

        $productsData = array();
        foreach ($products as $product) {
            $productsData[] = $this->productData($product);
        }

        return $productsData;
    }

    protected function getProductIdFromOrderItem($item)
    {
        $productId = $item->getProductId();
        $product   = Mage::getModel('catalog/product')->load($productId);

        if (in_array($product->getTypeId(), static::$complexProductTypes)) {
            return $productId;
        }

        $superProductConfig = $item->getBuyRequest()->getSuperProductConfig();
        if (!empty($superProductConfig['product_id'])) {
            return (int) $superProductConfig['product_id'];
        }

        $productEmulator = new Varien_Object();
        foreach (static::$complexProductTypes as $key => $typeId) {
            $productEmulator->setTypeId($typeId);
            $productType = Mage::getSingleton('catalog/product_type')->factory($productEmulator);
            $parentIds = $productType->getParentIdsByChild($productId);
            if (!empty($parentIds[0])) {
                $productId = $parentIds[0];
                break;
            }
        }

        return $productId;
    }

    protected function productData($product)
    {
        return $this->productHelper->getProductData($product);
    }

    protected function purchaseData(Mage_Sales_Model_Order $order)
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
