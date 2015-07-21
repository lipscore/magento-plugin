<?php

class Lipscore_RatingsReviews_Model_Purchase_Reminder
{
    protected $lipscoreConfig;
    protected $sender;
    
    const LOG_FILE = 'lipscore_reminder.log';
    
    public function __construct($params)
    {
        $websiteCode = isset($params['websiteCode']) ? $params['websiteCode'] : null;
        $storeCode = isset($params['storeCode']) ? $params['storeCode'] : null;
        
        $this->lipscoreConfig = Mage::helper('lipscore_ratingsreviews/config')->getScoped($websiteCode, $storeCode);
        $this->sender         = Mage::getModel('lipscore_ratingsreviews/api_request', array(
            'lipscoreConfig' => $this->lipscoreConfig,
            'path'           => 'purchases'
        ));
    }
    
    public function send($orders)
    {
        $data = array();
        $dataHelper = Mage::helper('lipscore_ratingsreviews/reminder');
        
        foreach ($orders as $order) {
            $data[] = $dataHelper->orderData($order);
        }
        
        return $this->sender->send(array('purchases' => $data));
    }
    
    protected function log($isSuccessful)
    {
        $result = $isSuccessful ? 'Reminders were created: ' : 'Reminders weren\'t created: ';
        Mage::log($result . $this->getResponseMsg(), Zend_Log::INFO, self::LOG_FILE);
    }
}
