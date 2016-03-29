<?php

class Lipscore_RatingsReviews_Model_Observer_Order_Status extends Lipscore_RatingsReviews_Model_Observer_Abstract
{
    protected static $logFile = 'observer_status';

    protected function fetch(Varien_Event_Observer $observer)
    {
        $this->log(date('Y-m-d H:i:s') . ' start fetch');
        $order = $observer->getEvent()->getOrder();

        $savedStatus = $this->fetchFromRegistery($order);
        $this->log('saved status: ' . $savedStatus);
        if (!$savedStatus) {
            $this->saveToRegistery($order);
        }
    }

    protected function check(Varien_Event_Observer $observer)
    {
        $this->log(date('Y-m-d H:i:s') . ' start check');
        $order = $observer->getEvent()->getOrder();

        $config = $this->config($order->getStore());
        if (!$config->isValidApiKey()) {
            return;
        }

        $oldStatus = $this->fetchFromRegistery($order);
        $this->log('old status: ' . $oldStatus);
        $currentStatus = $order->getStatus();
        $this->log('current status: ' . $currentStatus);
        $statusChanged = ($oldStatus != $currentStatus);
        $this->log('status change: ' . (int) $statusChanged);
        if (!$statusChanged) {
            return;
        }

        $properStatus = $this->isReminderableStatus($currentStatus, $config);
        $this->log('proper status: ' . (int) $properStatus);
        if ($properStatus) {
            $this->log('SEND!');
            $res = $this->reminder($config)->sendSingle($order);
            $this->log($res);
        }
    }

    private function fetchFromRegistery($order)
    {
        $key = $this->statusKey($order);
        $this->log('fetch key: ' . $key);
        return $key ? Mage::registry($key) : '';
    }

    private function saveToRegistery($order)
    {
        $key = $this->statusKey($order);
        $this->log('save key: ' . $key);
        if ($key) {
            return Mage::register($key, $order->getStatus());
        }
    }

    private function statusKey($order)
    {
        $orderId = $order->getId();
        return $orderId ? "lipscore_order_status_$orderId" : '';
    }

    private function isReminderableStatus($status, $config)
    {
        $reminderableStatus = $config->singleReminderStatus();
        $this->log('reminderable status: ' . $reminderableStatus);
        if (!$reminderableStatus) {
            return false;
        } else {
            return strtolower($status) == strtolower($reminderableStatus);
        }
    }

    private function reminder($config)
    {
        return Mage::getModel('lipscore_ratingsreviews/purchase_reminder', array('config' => $config));
    }

    private function config($store)
    {
        return Mage::getModel('lipscore_ratingsreviews/config', array('store' => $store));
    }

    protected function methodAvailable($method)
    {
        return $this->moduleHelper->isLipscoreModuleEnabled();
    }
}
