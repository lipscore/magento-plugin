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
    
    protected $store   = null;
    protected $website = null;
    
    public function __construct($params = array())
    {
        !empty($params['store'])   and $this->store   = $params['store'];
        !empty($params['website']) and $this->website = $params['website'];
    }

    public function get($param, $type)
    {
        $key = self::$_systemConfigs[$type] . $param;
        return $this->getMageConfig($key);
    }
    
    public function getMageConfig($key)
    {
        if ($this->store) {
            return $this->store->getConfig($key);
        }
        if ($this->website) {
            return $this->website->getConfig($key);
        }
        return Mage::getStoreConfig($key);        
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
