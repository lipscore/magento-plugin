<?php

class Lipscore_RatingsReviews_Model_Purchase_Reminder
{
    protected $dataHelper;
    protected $timeout;

    public function __construct($params)
    {
        $this->timeout    = isset($params['timeout']) ? $params['timeout'] : null;
        $this->dataHelper = Mage::helper('lipscore_ratingsreviews/reminder');
    }

    public function sendSingle($order)
    {
        $data = $this->dataHelper->orderData($order);
        $sender = $this->sender($order->getStore());
        return $sender->send($data);
    }

    public function sendMultiple($orders)
    {
        $data = array();

        foreach ($orders as $order) {
            $data[] = $this->dataHelper->orderData($order);
        }
        // TODO use correct sender
        // return $this->sender()->send(array('purchases' => $data));
    }

    protected function sender($store) {
        $config = Mage::helper('lipscore_ratingsreviews/config')->getScoped(null, $store->getCode());
        return Mage::getModel('lipscore_ratingsreviews/api_request', array(
            'lipscoreConfig' => $config,
            'path'           => 'purchases',
            'timeout'        => $this->timeout
        ));
    }
}
