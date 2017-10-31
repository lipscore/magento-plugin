<?php

class Lipscore_RatingsReviews_Helper_Kickstart extends Lipscore_RatingsReviews_Helper_Abstract
{
    const CRON_STRING_PATH = 'crontab/jobs/send_kickstart_orders/schedule/cron_expr';
    protected $kickstartConfig;

    public function dateFormat()
    {
        return Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }

    public function getOrders($store, $period, $statuses)
    {
        return Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('created_at', $period)
            ->addAttributeToFilter('status', array('in' => $statuses))
            ->addAttributeToFilter('store_id', array('eq' => $store->getId()));
    }

    public function resultData($storeName, $count, $error)
    {
        return array('store' => $storeName, 'err' => $error, 'count' => $count);
    }

    public function period($from, $to)
    {
        $from->setTime('00:00:00');
        $to->setTime('23:59:59');
        return array('datetime' => true, 'from' => $from, 'to' => $to);
    }

    public function saveTempResult($processed)
    {
        return $this->saveResult($processed, array(), false);
    }

    public function saveFinalResult($processed, $results)
    {
        return $this->saveResult($processed, $results, true);
    }

    protected function saveResult($processed, $results, $completed)
    {
        $result = array(
            'processed' => $processed,
            'stores'    => $results,
            'completed' => $completed
        );
        return $this->kickstartConfig()->setResult($result);
    }

    protected function kickstartConfig()
    {
        if (!$this->kickstartConfig) {
            $this->kickstartConfig = Mage::getModel('lipscore_ratingsreviews/config_kickstart');
        }
        return $this->kickstartConfig;
    }
}
