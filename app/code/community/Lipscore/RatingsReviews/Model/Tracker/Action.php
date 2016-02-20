<?php
class Lipscore_RatingsReviews_Model_Tracker_Action
{
    const APP_INSTALLED   = 'installed';
    const API_KEY_UPDATED = 'api_key_updated';
    
    public function track($actionType, $website)
    {
        $lipscoreConfig = Mage::helper('lipscore_ratingsreviews/config')->getScoped($website->getCode(), null);
        
        $installationId = $lipscoreConfig->pluginInstallationId();
        if (empty($installationId)) {
            return false;
        }        
        
        $sender = $this->getSender($lipscoreConfig, $installationId);
        $data = array('action_type' => $actionType);
        
        return $sender->send($data);
    }
    
    protected function getSender($lipscoreConfig, $installationId)
    {
        return Mage::getModel('lipscore_ratingsreviews/api_request', array(
            'lipscoreConfig' => $lipscoreConfig,
            'path'           => "plugin_installations/$installationId/actions"
        ));
    }
}
