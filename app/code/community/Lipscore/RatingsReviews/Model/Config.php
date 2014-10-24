<?php

class Lipscore_RatingsReviews_Model_Config
{
    protected static $_systemConfigs = array(
        'coupon'     => 'lipscore_coupons/coupons/',
        'identifier' => 'lipscore_general/product_identifier/',
        'brand'      => 'lipscore_general/product_brand/',
        'apiKey'     => 'lipscore_general/api_key/',
        'locale'     => 'lipscore_general/locale/',
    );

    public function get($param, $type)
    {
        return Mage::getStoreConfig(self::$_systemConfigs[$type] . $param, Mage::app()->getStore());
    }
    
    public function apiKey()
    {
        return $this->get('api_key', 'apiKey');
    }
    
    public function demoApiKey()
    {
        return $this->get('demo_api_key', 'apiKey');
    }
    
    public function locale()
    {
        return $this->get('locale', 'locale');
    }
    
    public function brandAttr()
    {
        return $this->get('attr', 'brand');
    }
    
    public function identifierType()
    {
        return $this->get('type', 'identifier');
    }
    
    public function identifierAttr()
    {
        return $this->get('attr', 'identifier');
    }
}
