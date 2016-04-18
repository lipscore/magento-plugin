<?php

class Lipscore_RatingsReviews_Model_Purchase_Reminder
{
    protected $dataHelper;
    protected $config;

    public function __construct($params)
    {
        $this->dataHelper = Mage::helper('lipscore_ratingsreviews/reminder');
        $this->config     = $params['config'];
    }

    public function sendSingle($order)
    {
        if (!$this->config->isValidApiKey()) {
            return false;
        }

        $sender = $this->sender($this->config->singleReminderTimeout());
        $data = $this->dataHelper->singleReminderData($order);
        return $sender->send($data);
    }

    public function sendMultiple($orders, $batchNumber, $totalOrderCount, &$processed)
    {
        if (!$this->config->isValidApiKey()) {
            return false;
        }

        $kickstartHelper = Mage::helper('lipscore_ratingsreviews/kickstart');
        $data = array();

        foreach ($orders as $order) {
            $data[] = $this->dataHelper->multipleReminderData($order);
            $processed++;
            $kickstartHelper->saveTempResult($processed);
        }

        $sender = $this->sender($this->config->multipleReminderTimeout());
        return $sender->send(array(
            'purchases'             => $data,
            'kickstart'             => true,
            'kickstart_batch'       => $batchNumber,
            'kickstart_total_count' => $totalOrderCount
        ));
    }

    protected function sender($timeout)
    {
        return Mage::getModel('lipscore_ratingsreviews/api_request', array(
            'lipscoreConfig' => $this->config,
            'path'           => 'purchases',
            'timeout'        => $timeout
        ));
    }
}
