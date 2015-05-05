<?php

class Lipscore_RatingsReviews_Model_Purchase_Reminder
{
    protected $lipscoreConfig = null;
    protected $response       = null;
    
    const LOG_FILE = 'lipscore_reminder.log';
    
    public function __construct($params)
    {
        $websiteCode = isset($params['websiteCode']) ? $params['websiteCode'] : null;
        $storeCode = isset($params['storeCode']) ? $params['storeCode'] : null;
        
        $this->lipscoreConfig = Mage::helper('lipscore_ratingsreviews/config')->getScoped($websiteCode, $storeCode);
    }
    
    public function send($orders)
    {
        $data = array();
        $dataHelper = Mage::helper('lipscore_ratingsreviews/reminder');
        
        foreach ($orders as $order) {
            $data[] = $dataHelper->orderData($order);
        }
        
        return $this->sendRequest(array('purchases' => $data));
    }
    
    public function getResponseMsg()
    {
        return $this->response ? $this->response->__toString() : '';
    }
    
    protected function sendRequest($data)
    {
        $apiKey = $this->lipscoreConfig->apiKey();
        $apiUrl = Mage::getModel('lipscore_ratingsreviews/config_env')->apiUrl();
        
        $client = new Zend_Http_Client("http://$apiUrl/purchases?api_key=$apiKey", array(
            'timeout' => 300
        ));
        $client->setRawData(json_encode($data), 'application/json')
               ->setMethod(Zend_Http_Client::POST);
        
        $this->response = $client->request();
        $result = $this->response->isSuccessful();
        
        $this->log($result);
                    
        return $result;
    }
    
    protected function log($isSuccessful)
    {
        $result = $isSuccessful ? 'Reminders were created: ' : 'Reminders weren\'t created: ';
        Mage::log($result . $this->getResponseMsg(), Zend_Log::INFO, self::LOG_FILE);
    }
}
