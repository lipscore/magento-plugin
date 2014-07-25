<?php

abstract class Lipscore_RatingsReviews_Helper_Abstract extends Mage_Core_Helper_Abstract
{
    /**
     * @var Lipscore_RatingsReviews_Model_Config
     */
    protected $_lipscoreConfig;
    
    public function __construct()
    {
        $this->_lipscoreConfig = Mage::getModel('lipscore_ratingsreviews/config');
    }
}
