<?php 

class Lipscore_RatingsReviews_Model_Config_Env extends Varien_Simplexml_Config
{
    const DEV_ENV  = 'development';
    const PROD_ENV = 'production';
    
    const CACHE_TAG = 'config_lipscore_env';
    const CACHE_ID  = 'config_lipscore_env';
    
    public function __construct()
    {
        $canUserCache = Mage::app()->useCache('config');
        if ($canUserCache) {
            $this->setCacheId(self::CACHE_ID)
                ->setCacheTags(array(self::CACHE_TAG))
                ->setCacheChecksum(null)
                ->setCache(Mage::app()->getCache());

            if ($this->loadCache()) {
                return;
            }
        }
        
        $env = getenv('MAGE_ENV') == self::DEV_ENV ? self::DEV_ENV : self::PROD_ENV;
        $configFile = Mage::getModuleDir('etc', 'Lipscore_RatingsReviews'). DS . 'environments' . DS . $env . '.xml';
        parent::__construct($configFile);
        
        if ($canUserCache) {
            $this->saveCache();
        }
    }
    
    public function apiUrl()
    {
        return $this->getNode('lipscore_api_url');
    }
    
    public function assetsUrl()
    {
        return $this->getNode('lipscore_assets_url');
    }
}
