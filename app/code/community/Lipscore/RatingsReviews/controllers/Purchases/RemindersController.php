<?php

class Lipscore_RatingsReviews_Purchases_RemindersController extends Mage_Adminhtml_Controller_Action
{
    protected $lipscoreConfig = null;

    public function preDispatch()
    {
        Mage::setIsDeveloperMode(true);
        if (!$this->getRequest()->isAjax()) {
            Lipscore_RatingsReviews_Logger::logException(new Exception('Non-ajax request to sending reminders action'));
            $this->_forward('noRoute');
            return;
        }
        parent::preDispatch();
    }

    public function previewAction()
    {
        try {
            $this->preview();
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
            $this->response(false, $e->getMessage() . '\n' . $e->getTraceAsString());
        }
    }

    public function sendAction()
    {
        return false;
        try {
            $this->send();
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
            $this->response(false, $e->getMessage() . '\n' . $e->getTraceAsString());
        }
    }

    protected function preview()
    {
        $period   = $this->getPeriod();
        $statuses = $this->getStatuses();
        $stores   = $this->getStores();

        $data = array();

        foreach ($stores as $key => $store) {
            $orders = $this->getOrders($store, $period, $statuses)->getSize();
            $config = Mage::helper('lipscore_ratingsreviews/config')->getScoped($store->getWebsite(), $store);
            $data[] = array(
                'website' => $store->getWebsite()->getName(),
                'name'    => $store->getName(),
                'group'   => $store->getGroup(),
                'demo'    => $config->isDemoKey(),
                'orders'  => $orders
            );
        }
        $this->response(true, $data);
    }

    protected function send()
    {
        //$this->checkKey();
        $stores = $this->getStores();

        $period   = $this->getPeriod();
        $statuses = $this->getStatuses();

        $orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('created_at', $period)
            ->addAttributeToFilter('status', array('in' => $statuses));

        $store = $this->getStore();
        if ($store) {
            $orders->addAttributeToFilter('store_id', array('eq' => $store->getId()));
        }

        if (!count($orders)) {
            $this->response(false, 'No orders found for a selected period.');
        }

        $sender = Mage::getModel('lipscore_ratingsreviews/purchase_reminder', array(
            'websiteCode' => $this->getWebsiteCode(),
            'storeCode'   => $this->getStoreCode()
        ));

        $result = $sender->sendMultiple($orders);
        if ($result) {
            $this->response(true, "Emails were scheduled successfully.");
        } else {
            $this->response(false, $sender->getResponseMsg());
        }
    }

    protected function checkKey()
    {
        $apiKey = $this->getLipscoreConfig()->apiKey();
        if (!$apiKey) {
            $this->response(false, 'You should provide your Api Key and save config.');
        }
    }

    protected function getPeriod()
    {
        $data   = $this->getRequest()->getParams();
        $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        try {
            $startDate = empty($data['from']) ? false : new Zend_Date($data['from'], $format);
            $endDate   = empty($data['to']) ? false : new Zend_Date($data['to'], $format);
        } catch (Exception $e) {
            $startDate = false;
            $endDate   = false;
        }

        $correctPeriod = $startDate && $endDate ? $startDate->compare($endDate) <= 0 : $startDate || $endDate;
        if (!$correctPeriod) {
            $this->response(false, 'Please set a correct period.');
        }

        $result = array('datetime' => true);
        if ($startDate) {
            $result['from'] = $startDate;
        }
        if ($endDate) {
            $result['to'] = $endDate;
        }

        return $result;
    }

    protected function getStatuses()
    {
        $statuses = $this->getRequest()->getParam('status', array());
        if (empty($statuses)) {
            $this->response(false, 'Please select order status(es).');
        }
        return $statuses;
    }

    protected function response($result, $response)
    {
        $body = Zend_Json::encode(array('message' => $response));
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody($body);

        if (!$result) {
            $this->getResponse()->setHttpResponseCode(422);
        }

        $this->getResponse()->sendResponse();

        exit(0);
    }

    protected function getLipscoreConfig()
    {
        if (!$this->lipscoreConfig) {
            $websiteCode = $this->getWebsiteCode();
            $storeCode   = $this->getStoreCode();

            $this->lipscoreConfig = Mage::helper('lipscore_ratingsreviews/config')->getScoped($websiteCode, $storeCode);
        }
        return $this->lipscoreConfig;
    }

    protected function getOrders($store, $period, $statuses)
    {
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('created_at', $period)
            ->addAttributeToFilter('status', array('in' => $statuses))
            ->addAttributeToFilter('store_id', array('eq' => $store->getId()));
    }

    protected function getStores()
    {
        $stores = array();

        $websiteCode = $this->getWebsiteCode();
        $storeCode   = $this->getStoreCode();

        if ($storeCode) {
            $store = Mage::getModel('core/store')->load($storeCode);
            $stores = array($store);
        } elseif ($websiteCode) {
            $website = Mage::getModel('core/website')->load($websiteCode);
            $stores = $website->getStores();
        } else {
            $stores = Mage::app()->getStores();
        }

        return $stores ? $stores : array();
    }

    protected function getWebsiteCode()
    {
        return $this->getRequest()->getParam('website');
    }

    protected function getStoreCode()
    {
        return $this->getRequest()->getParam('store');
    }
}
