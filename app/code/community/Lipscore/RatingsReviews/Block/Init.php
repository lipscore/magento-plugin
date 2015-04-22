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
    
    protected static $_availableLocales = array('en', 'it', 'no', 'es', 'br');
    
    public function __construct()
    {
        $this->_lipscoreConfig = Mage::getModel('lipscore_ratingsreviews/config');
        $this->_envConfig      = Mage::getModel('lipscore_ratingsreviews/config_env');
            
        parent::_construct();
    }
    
    protected function _beforeToHtml()
    {
        try {
            $this->setLipscoreApiKey($this->_lipscoreConfig->apiKey());
            $this->setAssetsUrl($this->_envConfig->assetsUrl());
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }
            
        return parent::_beforeToHtml();
    }    
        
    protected function getLipscoreLocale()
    {
        $locale = null;        
        try {
            $locale = $this->_lipscoreConfig->locale();
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }
        
        if ($locale == 'auto') {
            $storeLocale = null;
            try {
                $storeLocale = Mage::app()->getLocale()->getLocale()->getLanguage();
            } catch (Exception $e) {
                Lipscore_RatingsReviews_Logger::logException($e);
            }
                
            $locale = in_array($storeLocale, self::$_availableLocales) ? $storeLocale : null;
        }
        return $locale ? $locale . '/' : '';
    }
}
