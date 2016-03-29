<?php

class Lipscore_RatingsReviews_Purchases_RemindersController extends Mage_Adminhtml_Controller_Action
{
    protected $kickstartHelper;

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
            $this->response(false, $e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }

    public function sendAction()
    {
        try {
            $this->send();
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
            $this->response(false, $e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }

    protected function preview()
    {
        $period   = $this->getPeriod();
        $statuses = $this->getStatuses();

        $statusNames = array();
        foreach ($statuses as $key => $statusCode) {
            $statusNames[] = Mage::getSingleton('sales/order_config')->getStatusLabel($statusCode);
        }
        $format = $this->kickstartHelper()->dateFormat();

        $results = array(
            'statuses' => $statusNames,
            'from'     => $period['from']->toString($format),
            'to'       => $period['to']->toString($format),
            'stores'   => array()
        );

        $stores = $this->getStores();
        foreach ($stores as $key => $store) {
            $storeName = $store->getName();
            $config = $this->config($store);
            if (!$config->apiKey()) {
                $results['stores'][] = $this->kickstartHelper()->resultData($storeName, 0, 'invalid_key');
                continue;
            }
            if ($config->isDemoKey()) {
                $results['stores'][] = $this->kickstartHelper()->resultData($storeName, 0, 'demo_key');
                continue;
            }
            $storeOrders = $this->kickstartHelper()->getOrders($store, $period, $statuses);
            $ordersCount = count($storeOrders);
            if ($ordersCount) {
                $results['stores'][] = $this->kickstartHelper()->resultData($storeName, $ordersCount, null);
            } else {
                $results['stores'][] = $this->kickstartHelper()->resultData($storeName, 0, 'no_orders');
            }
        }
        if ($results) {
            $this->response(true, $results);
        } else {
            $this->response(false, 'No orders found.');
        }
    }

    protected function send()
    {
        $period   = $this->getPeriod();
        $statuses = $this->getStatuses();

        $results = array();
        $foundStores  = array();

        $stores = $this->getStores();
        foreach ($stores as $key => $store) {
            $storeName = $store->getName();
            $config = $this->config($store);
            if (!$config->apiKey()) {
                $results[] = $this->kickstartHelper()->resultData($storeName, 0, 'invalid_key');
                continue;
            }
            if ($config->isDemoKey()) {
                $results[] = $this->kickstartHelper()->resultData($storeName, 0, 'demo_key');
                continue;
            }
            $storeOrders = $this->kickstartHelper()->getOrders($store, $period, $statuses);
            if (count($storeOrders)) {
                $foundStoreIds[] = $store->getId();
            } else {
                $results[] = $this->kickstartHelper()->resultData($storeName, 0, 'no_orders');
            }
        }

        if ($foundStoreIds) {
            sleep(10);
            $filePath = Mage::getBaseDir('var') . '/log/' . 'async' . '.log';
            file_put_contents($filePath, print_r('done', true) . "\n", FILE_APPEND);
            //$this->kickstartHelper()->schedule($foundStoreIds, $statuses, $period['from'], $period['to']);
        }

        if ($results) {
            $this->response(true, $results);
        } else {
            $this->response(false, 'No orders found.');
        }
    }

    protected function getPeriod()
    {
        $data   = $this->getRequest()->getParams();
        $format = $this->kickstartHelper()->dateFormat();

        try {
            $startDate = empty($data['from']) ? false : new Zend_Date($data['from'], $format);
            $endDate   = empty($data['to']) ? false : new Zend_Date($data['to'], $format);
        } catch (Exception $e) {
            $startDate = false;
            $endDate   = false;
        }

        $correctPeriod = $startDate && $endDate && $startDate->compare($endDate) <= 0;
        if (!$correctPeriod) {
            $this->response(false, 'Please set a correct period.');
        }

        return $this->kickstartHelper()->period($startDate, $endDate);
    }

    protected function getStatuses()
    {
        $statuses = $this->getRequest()->getParam('status', array());
        if (empty($statuses)) {
            $this->response(false, 'Please select order status(es).');
        }
        return $statuses;
    }

    protected function getStores()
    {
        $stores = array();

        $websiteCode = $this->getRequest()->getParam('website');
        $storeCode   = $this->getRequest()->getParam('store');

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

    protected function response($result, $response)
    {
        $body = Zend_Json::encode(array('data' => $response));
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody($body);

        if (!$result) {
            $this->getResponse()->setHttpResponseCode(422);
        }

        $this->getResponse()->sendResponse();

        exit(0);
    }

    protected function config($store)
    {
        return Mage::getModel('lipscore_ratingsreviews/config', array('store' => $store));
    }

    protected function kickstartHelper()
    {
        if (!$this->kickstartHelper) {
            $this->kickstartHelper = Mage::helper('lipscore_ratingsreviews/kickstart');
        }
        return $this->kickstartHelper;
    }
}
