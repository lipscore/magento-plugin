<?php

abstract class Lipscore_RatingsReviews_Model_Observer_Abstract
{
    protected static $logFile = 'observer';

    protected $moduleHelper;

    public function __construct()
    {
        $this->moduleHelper = Mage::helper('lipscore_ratingsreviews/module');
    }

    public function __call($method, $arguments) {
        try {
            $this->log($method);
            if ($this->methodAvailable($method)) {
                return call_user_func_array(array($this, $method), $arguments);
            }
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }
    }

    abstract protected function methodAvailable($method);

    protected function log($message)
    {
        if (!getenv('LIPSCORE_LOG_OBSERVER')) {
            return;
        }

        $filePath = Mage::getBaseDir('var') . DS . 'log' . DS . static::$logFile . '.log';
        file_put_contents($filePath, print_r($message, true) . "\n", FILE_APPEND);
    }

    protected function disableModuleOutput($moduleName)
    {
        $outputPath = 'advanced/modules_disable_output/' . $moduleName;
        if (!Mage::getStoreConfig($outputPath)) {
            Mage::app()->getStore()->setConfig($outputPath, true);
        }
    }
}
