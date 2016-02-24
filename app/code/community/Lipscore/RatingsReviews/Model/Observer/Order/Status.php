<?php

class Lipscore_RatingsReviews_Model_Observer_Order_Status extends Lipscore_RatingsReviews_Model_Observer_Abstract
{
    public function fetch(Varien_Event_Observer $observer)
    {
        try {
            $this->_fetch($observer);
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }
    }

    private function _fetch(Varien_Event_Observer $observer)
    {
        $this->log(date('Y-m-d H:i:s') . ' start fetch');
        $order = $observer->getEvent()->getOrder();

        $savedStatus = $this->fetchFromRegistery($order);
        $this->log('saved status: ' . $savedStatus);
        if (!$savedStatus) {
            $this->saveToRegistery($order);
        }
    }

    public function check(Varien_Event_Observer $observer)
    {
        try {
            $this->_check($observer);
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }
    }

    private function _check(Varien_Event_Observer $observer)
    {
        $this->log(date('Y-m-d H:i:s') . ' start check');
        $order = $observer->getEvent()->getOrder();
        $storeId = $order->getStoreId();

        $oldStatus = $this->fetchFromRegistery($order);
        $this->log('old status: ' . $oldStatus);
        $currentStatus = $observer->getOrder()->getStatus();
        $this->log('current status: ' . $currentStatus);
        $statusChanged = ($oldStatus != $currentStatus);
        $this->log('status change: ' . (int) $statusChanged);
        if (!$statusChanged) {
            return;
        }

        $properStatus = $this->isReminderableStatus($currentStatus, $storeId);
        $this->log('proper status: ' . (int) $properStatus);
        if ($properStatus) {
            $this->log('SEND!');
            $res = $this->reminder($storeId)->sendSingle($order);
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

    private function isReminderableStatus($status, $storeId)
    {
        $reminderableStatus = $this->config($storeId)->singleReminderStatus();
        $this->log('reminderable status: ' . $reminderableStatus);
        if (!$reminderableStatus) {
            return false;
        } else {
            return strtolower($status) == strtolower($reminderableStatus);
        }
    }

    private function reminder($storeId)
    {
        return Mage::getModel(
            'lipscore_ratingsreviews/purchase_reminder',
            array('timeout' => $this->config($storeId)->singleReminderTimeout())
        );
    }

    private function config($storeId)
    {
        return Mage::helper('lipscore_ratingsreviews/config')->getScoped(null, $storeId);
    }

    private function log($message)
    {
        //file_put_contents(Mage::getBaseDir('var') . '/log/order_status.log', print_r($message, true) . "\n", FILE_APPEND);
    }
}
