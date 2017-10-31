<?php

class Lipscore_RatingsReviews_Model_Config_Kickstart extends Lipscore_RatingsReviews_Model_Config_Abstract
{
    protected static $_systemConfigs = array(
        'kikstart' => 'lipscore_reminder/kikstart/'
    );

    public function resultJson()
    {
        return $this->get('result', 'kikstart');
    }

    public function setResult($data)
    {
        $data['updated_at'] = time();
        return $this->set('result', 'kikstart', Zend_Json::encode($data));
    }

    public function clearResult()
    {
        return $this->set('result', 'kikstart', null);
    }

    public function getMageConfig($path)
    {
        return Mage::getConfig()->getNode($path, 'default');
    }

    public function setMageConfig($path, $value)
    {
        return $this->saveConfig($path, $value, 'default', 0);
    }
}
