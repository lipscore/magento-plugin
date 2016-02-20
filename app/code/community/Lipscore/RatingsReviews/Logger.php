<?php

class Lipscore_RatingsReviews_Logger
{
    public static function logException(Exception $e)
    {
        Mage::logException($e);

        $store = null;
        $storeInfo = $url = $to = '';

        try {
            $store = Mage::app()->getStore();
        } catch (Exception $e) {}

        if ($store) {
            $storeUrl  = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
            $storeInfo = $store->getFrontendName() . ', ' . self::_url($storeUrl);
            $url       = $store->getCurrentUrl();
        } else {
            $storeInfo = 'N/A';
        }

        if (!$url && isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI'])) {
            $url  = isset($_SERVER['HTTPS']) ? 'https' : 'http';
            $url .= '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        }

        $errMsg  = $e->getMessage();
        $trace   = $e->getTraceAsString();
        $link    = self::_url($url);
        $version = Mage::getVersion();
        $sbj     = "Magento extension error: $errMsg";
        $msg     = "STORE: $storeInfo, $version\n\nERROR MESSAGE: $errMsg\n\nURL: $link\n\nSTACK TRACE: $trace";

        try {
            $config = Mage::getModel('lipscore_ratingsreviews/config_env');
            $to = (string) $config->errorsEmail();
        } catch (Exception $e) {}

        if (!empty($to)) {
            try {
                self::_sendEmail($to, $sbj, $msg);
            } catch (Exception $e) {}
        }
    }

    protected static function _url($url)
    {
        return $url ? $url : 'N/A';
    }

    protected static function _sendEmail($to, $sbj, $msg)
    {
        $fromEmail = Mage::getStoreConfig('trans_email/ident_general/email');
        $fromName  = Mage::getStoreConfig('trans_email/ident_general/name');

        $mail = Mage::getModel('core/email')
            ->setFromEmail($fromEmail)
            ->setFromName($fromName)
            ->setToEmail($to)
            ->setSubject($sbj)
            ->setBody($msg)
            ->send();
    }
}
