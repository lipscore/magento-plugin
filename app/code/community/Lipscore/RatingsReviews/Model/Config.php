<?php

class Lipscore_RatingsReviews_Model_Config
{
    protected static $_configs = array(
        'coupon'     => 'lipscore_coupons/coupons/',
        'identifier' => 'lipscore_general/product_identifier/',
        'brand'      => 'lipscore_general/product_brand/',
        'apiKey'     => 'lipscore_general/api_key/'
    );
    
    public function get($param, $type)
    {
        return Mage::getStoreConfig(self::$_configs[$type] . $param, Mage::app()->getStore());
    }
}
