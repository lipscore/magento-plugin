<?php

class Lipscore_RatingsReviews_Helper_Locale extends Lipscore_RatingsReviews_Helper_Abstract
{
    protected static $_availableLocales = array('en', 'it', 'no', 'es', 'br', 'ru', 'se', 'cz', 'nl', 'dk', 'ja', 'de', 'fi');

    public function getLipscoreLocale()
    {
        $locale = null;
        try {
            $locale = $this->_lipscoreConfig->locale();
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }

        if ($locale == 'auto') {
            $locale = null;
            try {
                $locale = $this->getFromStore();
            } catch (Exception $e) {
                Lipscore_RatingsReviews_Logger::logException($e);
            }
        }
        return $locale;
    }

    public function getStoreLocale()
    {
        $locale = '';
        try {
            $locale = $this->getLipscoreLocale();
            if (!$locale) {
                $localeCode = $this->getStoreLocaleCode();
                list($locale, $region) = explode('_', $localeCode);
            }
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }
        return $locale ? $locale : 'en';
    }

    protected function getFromStore()
    {
        $localeCode = $this->getStoreLocaleCode();
        list($language, $region) = explode('_', $localeCode);

        $locale = $this->getAvailableLocale($language);
        if (is_null($locale)) {
            $locale = $this->getAvailableLocale($region);
        }
        return $locale;
    }

    protected function getStoreLocaleCode()
    {
        return $this->_lipscoreConfig->getMageConfig('general/locale/code');
    }

    protected function getAvailableLocale($storeLocale)
    {
        $storeLocale = strtolower($storeLocale);
        return in_array($storeLocale, self::$_availableLocales) ? $storeLocale : null;
    }
}
