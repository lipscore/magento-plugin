<?php

class Lipscore_RatingsReviews_Model_Config extends Lipscore_RatingsReviews_Model_Config_Abstract
{
    const SINGLE_REMINDER_TIMEOUT   = 5;
    const MULTIPLE_REMINDER_TIMEOUT = 1800;

    protected static $_systemConfigs = array(
        'coupon'   => 'lipscore_coupons/coupons/',
        'brand'    => 'lipscore_general/product_brand/',
        'apiKey'   => 'lipscore_general/api_key/',
        'locale'   => 'lipscore_general/locale/',
        'emails'   => 'lipscore_general/emails/',
        'module'   => 'lipscore_general/module/',
        'tracking' => 'lipscore_plugin/'
    );

    public function apiKey()
    {
        return $this->get('api_key', 'apiKey');
    }

    public function secret()
    {
        return $this->get('secret', 'apiKey');
    }

    public function demoApiKey()
    {
        return $this->get('demo_api_key', 'apiKey');
    }

    public function locale()
    {
        return $this->get('locale', 'locale');
    }

    public function brandAttr()
    {
        return $this->get('attr', 'brand');
    }

    public function lastTrackedVersion()
    {
        return $this->get('last_tracked_version', 'tracking');
    }

    public function pluginInstallationId()
    {
        return $this->get('plugin_installation_id', 'tracking');
    }

    public function singleReminderStatus()
    {
        return $this->get('order_status', 'emails');
    }

    public function singleReminderTimeout()
    {
        $timeout = getenv('SINGLE_REMINDER_TIMEOUT');
        return $timeout ? $timeout : static::SINGLE_REMINDER_TIMEOUT;
    }

    public function multipleReminderTimeout()
    {
        $timeout = getenv('MULTIPLE_REMINDER_TIMEOUT');
        return $timeout ? $timeout : static::MULTIPLE_REMINDER_TIMEOUT;
    }

    public function isModuleActive()
    {
        return $this->get('active', 'module');
    }

    public function isDemoKey()
    {
        $currentKey = $this->apiKey();
        $demokey    = $this->demoApiKey();
        return $currentKey == $demokey;
    }

    public function isValidApiKey()
    {
        return $this->apiKey() && !$this->isDemoKey();
    }

    public function setLastTrackedVersion($value)
    {
        return $this->set('last_tracked_version', 'tracking', $value);
    }

    public function setPluginInstallationId($value)
    {
        return $this->set('plugin_installation_id', 'tracking', $value);
    }
}
