<?php

class Lipscore_RatingsReviews_Model_Purchase_Reminder
{
    protected $lipscoreConfig;
    protected $sender;
    protected $dataHelper;

    public function __construct($params)
    {
        $websiteCode = isset($params['websiteCode']) ? $params['websiteCode'] : null;
        $storeCode = isset($params['storeCode']) ? $params['storeCode'] : null;

        $this->lipscoreConfig = Mage::helper('lipscore_ratingsreviews/config')->getScoped($websiteCode, $storeCode);
        $this->sender         = Mage::getModel('lipscore_ratingsreviews/api_request', array(
            'lipscoreConfig' => $this->lipscoreConfig,
            'path'           => 'purchases',
            'timeout'        => isset($params['timeout']) ? $params['timeout'] : null
        ));
        $this->dataHelper = Mage::helper('lipscore_ratingsreviews/reminder');
    }

    public function sendSingle($order)
    {
        $data = $this->dataHelper->orderData($order);
        return $this->sender->send($data);
    }

    public function sendMultiple($orders)
    {
        $data = array();

        foreach ($orders as $order) {
            $data[] = $this->dataHelper->orderData($order);
        }

        return $this->sender->send(array('purchases' => $data));
    }
}
