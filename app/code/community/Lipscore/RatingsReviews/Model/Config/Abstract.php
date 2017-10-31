<?php

abstract class Lipscore_RatingsReviews_Model_Config_Abstract
{
    protected static $_systemConfigs = array();

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
        return $this->saveConfig($path, $value, $scope, $scopeId);
    }

    protected function saveConfig($path, $value, $scope, $scopeId)
    {
        $result = Mage::getConfig()->saveConfig($path, $value, $scope, $scopeId);
        Mage::getConfig()->cleanCache();
        return $result;
    }

    protected function getKey($param, $type)
    {
        return static::$_systemConfigs[$type] . $param;
    }
}
