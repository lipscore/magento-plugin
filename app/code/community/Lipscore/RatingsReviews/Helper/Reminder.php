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
        $productsData = array();
        $storeId = $order->getStoreId();
        $orderItems = $order->getAllVisibleItems();

        foreach ($orderItems as $orderItem) {
            $productId = $orderItem->getProductId();
            $product = Mage::getModel('catalog/product')->load($productId);

            $parentProductId = $this->getParentProductId($product, $orderItem);
            if ($parentProductId) {
                $product = Mage::getModel('catalog/product')->load($parentProductId);
            }

            $product->setStoreId($storeId);
            $data = $this->productHelper->getProductData($product);

            if (!$product->isVisibleInSiteVisibility() && !$parentProductId) {
                $store = Mage::getModel('core/store')->load($storeId);
                $data['url'] = $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
            }

            $productsData[$product->getId()] = $data;

            gc_collect_cycles();
        }

        return array_values($productsData);
    }

    protected function getParentProductId($product, $item)
    {
        $superProductConfig = $item->getBuyRequest()->getSuperProductConfig();
        if (!empty($superProductConfig['product_id'])) {
            return (int) $superProductConfig['product_id'];
        }

        if ($product->isVisibleInSiteVisibility()) {
            return;
        }

        $parentId = null;
        $childId = $product->getId();
        $productEmulator = new Varien_Object();
        foreach (static::$complexProductTypes as $key => $typeId) {
            $productEmulator->setTypeId($typeId);
            $productType = Mage::getSingleton('catalog/product_type')->factory($productEmulator);
            $parentIds = $productType->getParentIdsByChild($childId);
            if (!empty($parentIds[0])) {
                $parentId = $parentIds[0];
                break;
            }
        }

        return $parentId;
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
