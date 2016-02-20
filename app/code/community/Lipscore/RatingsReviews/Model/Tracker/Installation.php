<?php
class Lipscore_RatingsReviews_Model_Tracker_Installation
{
    public function track($website)
    {
        $lipscoreConfig = $this->getConfig($website);
        $data           = $this->getData($website);
        $sender         = $this->getSender($lipscoreConfig, 'POST');
        
        $result    = $sender->send($data);
        $isSuccess = $result && !empty($result['id']);
        if ($isSuccess) {
            $lipscoreConfig->setLastTrackedVersion($this->getPluginVersion());
            $lipscoreConfig->setPluginInstallationId($result['id']);
        }
        
        return $isSuccess;
    }

    public function trackUpgrade($website)
    {
        $lipscoreConfig = $this->getConfig($website);
        
        $installationId = $lipscoreConfig->pluginInstallationId();
        if (empty($installationId)) {
            return false;
        }
        
        $sender = $this->getSender($lipscoreConfig, 'PUT', $installationId);
        $result = $sender->send(array('plugin_version' => $this->getPluginVersion()));
        if ($result) {
            $lipscoreConfig->setLastTrackedVersion($this->getPluginVersion());
        }
        
        return !empty($result);
    }    
    
    protected function getData($website)
    {
        $shop         = Mage::getModel('lipscore_ratingsreviews/shop', array('website' => $website));
        $moduleHelper = Mage::helper('lipscore_ratingsreviews/module');
        
        return array(
            'name'             => $shop->name,
            'url'              => $shop->url,
            'contact_name'     => $shop->contactName,
            'contact_email'    => $shop->contactEmail,
            'country'          => $shop->country,
            'platform'         => 'magento',
            'platform_version' => Mage::getVersion(),
            'plugin_version'   => $moduleHelper->getversion(),
            'langs'            => $shop->langs            
        );
    }

    protected function getSender($lipscoreConfig, $requestType, $installationId = null)
    {
        $path = 'plugin_installations';
        if ($installationId) {
            $path .= "/$installationId";
        }
        return Mage::getModel('lipscore_ratingsreviews/api_request', array(
            'lipscoreConfig' => $lipscoreConfig,
            'path'           => $path,
            'requestType'    => $requestType
        ));
    }
    
    protected function getConfig($website)
    {
        return Mage::getModel('lipscore_ratingsreviews/config', array('website' => $website));
    }
    
    protected function getPluginVersion()
    {
        $moduleHelper = Mage::helper('lipscore_ratingsreviews/module');
        return $moduleHelper->getVersion();
    }
}
