<?php

abstract class Lipscore_RatingsReviews_Helper_Abstract extends Mage_Core_Helper_Abstract
{
    /**
     * @var Lipscore_RatingsReviews_Model_Config
     */
    protected $_lipscoreConfig;

    public function __call($method, $arguments) {
        try {
            call_user_func_array(array($this, $method), $arguments);
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }
    }

    public function __construct()
    {
        $this->_lipscoreConfig = Mage::getModel('lipscore_ratingsreviews/config');
    }

    public function setLipscoreConfig($lipscoreConfig)
    {
        $this->_lipscoreConfig = $lipscoreConfig;
    }
}
