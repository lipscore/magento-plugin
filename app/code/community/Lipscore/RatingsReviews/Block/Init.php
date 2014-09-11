<?php

class Lipscore_RatingsReviews_Block_Init extends Mage_Core_Block_Template
{
    /**
     * @var Lipscore_RatingsReviews_Model_Config
     */
    protected $_lipscoreConfig;
    
    protected static $_availableLocales = array('en', 'no');
    
    public function __construct()
    {
        $this->_lipscoreConfig = Mage::getModel('lipscore_ratingsreviews/config');
        parent::_construct();
    }
    
    protected function _beforeToHtml()
    {
        $this->setLipscoreApiKey($this->_lipscoreConfig->get('api_key', 'apiKey'));
    
        return parent::_beforeToHtml();
    }    
        
    protected function getLipscoreLocale()
    {
        $locale = $this->_lipscoreConfig->get('locale', 'locale');
        if ($locale == 'auto') {
            $storeLocale = Mage::app()->getLocale()->getLocale()->getLanguage();
            $locale = in_array($storeLocale, self::$_availableLocales) ? $storeLocale : null;
        }
        return $locale ? $locale . '/' : '';
    }
}
