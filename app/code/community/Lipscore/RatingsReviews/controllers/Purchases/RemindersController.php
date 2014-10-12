<?php

class Lipscore_RatingsReviews_Purchases_RemindersController extends Mage_Adminhtml_Controller_Action
{
    public function sendAction()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_forward('noRoute');
            return;
        }
        
        $this->_checkKey();
        
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('created_at', array('from' => $this->_getStartDate()))
            ->addAttributeToFilter('status', array('eq' => Mage_Sales_Model_Order::STATE_COMPLETE));
        
        if (!count($orders)) {
            $this->_response(false, 'No completed orders found for a selected period.');
        }
        
        $sentItems = Mage::getModel('lipscore_ratingsreviews/purchase_reminder')->send($orders);
        
        $this->_response(true, "$sentItems purchase reminders were created.");
    }
    
    protected function _checkKey()
    {
        $apiKey = Mage::getModel('lipscore_ratingsreviews/config')->apiKey();
        if (!$apiKey) {
            $this->_response(false, 'You should provide your Api Key and save config.');
        }        
    }
    
    protected function _getStartDate()
    {
        $data = $this->getRequest()->getParams();
        
        $startDate = empty($data['period']) ? false : strtotime('-' . $data['period']);        
        if (!$startDate) {
            $this->_response(false, 'Please select a correct period.');
        }
        
        return date('Y-m-d H:i:s', $startDate);
    }
    
    protected function _response($result, $response)
    {
        $body = Zend_Json::encode(array('message' => $response));
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody($body)
            ->setHttpResponseCode($result ? 200 : 422)
            ->sendResponse();
        
        exit(0);        
    }
}
