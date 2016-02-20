<?php

class Lipscore_RatingsReviews_Model_Config
{
    protected static $_systemConfigs = array(
        'coupon'     => 'lipscore_coupons/coupons/',
        'brand'      => 'lipscore_general/product_brand/',
        'apiKey'     => 'lipscore_general/api_key/',
        'locale'     => 'lipscore_general/locale/',
        'tracking'   => 'lipscore_plugin/'
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
        $key = $this->getKey($param, $type);
        return $this->getMageConfig($key);
    }
    
    public function set($param, $type, $value)
    {
        $key = $this->getKey($param, $type);
        return $this->setMageConfig($key, $value);
    }
    
    public function getMageConfig($path)
    {
        if ($this->store) {
            return $this->store->getConfig($path);
        }
        if ($this->website) {
            return $this->website->getConfig($path);
        }
        return Mage::getStoreConfig($path);        
    }

    public function setMageConfig($path, $value)
    {
        if ($this->website) {
            $scope   = 'websites';
            $scopeId = $this->website->getId();
        } else {
            $scope    = 'stores';
            $store   = $this->store ? $this->store : Mage::app()->getStore();
            $scopeId = $store->getId();
        }
        return Mage::getConfig()->saveConfig($path, $value, $scope, $scopeId);
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
    
    public function lastTrackedVersion()
    {
        return $this->get('last_tracked_version', 'tracking');
    }
    
    public function pluginInstallationId()
    {
        return $this->get('plugin_installation_id', 'tracking');
    }

    #public function pluginInstallationId()
    #{
    #    return $this->get('enabled', 'general');
    #}
    
    public function setLastTrackedVersion($value)
    {
        return $this->set('last_tracked_version', 'tracking', $value);
    }
    
    public function setPluginInstallationId($value)
    {
        return $this->set('plugin_installation_id', 'tracking', $value);
    }

    protected function getKey($param, $type)
    {
        return self::$_systemConfigs[$type] . $param;
    }
}
