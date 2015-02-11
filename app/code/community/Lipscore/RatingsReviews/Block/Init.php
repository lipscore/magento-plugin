<?php

class Lipscore_RatingsReviews_Block_Init extends Mage_Core_Block_Template
{
    /**
     * @var Lipscore_RatingsReviews_Model_Config
     */
    protected $_lipscoreConfig;
    
    /**
     * @var Lipscore_RatingsReviews_Model_Config_Env
     */
    protected $_envConfig;
    
    protected static $_availableLocales = array('en', 'it', 'no', 'es');
    
    public function __construct()
    {
        $this->_lipscoreConfig = Mage::getModel('lipscore_ratingsreviews/config');
        $this->_envConfig      = Mage::getModel('lipscore_ratingsreviews/config_env');
        
        parent::_construct();
    }
    
    protected function _beforeToHtml()
    {
        $this->setLipscoreApiKey($this->_lipscoreConfig->apiKey());
        $this->setAssetsUrl($this->_envConfig->assetsUrl());
    
        return parent::_beforeToHtml();
    }    
        
    protected function getLipscoreLocale()
    {
        $locale = $this->_lipscoreConfig->locale();
        if ($locale == 'auto') {
            $storeLocale = Mage::app()->getLocale()->getLocale()->getLanguage();
            $locale = in_array($storeLocale, self::$_availableLocales) ? $storeLocale : null;
        }
        return $locale ? $locale . '/' : '';
    }
}
