<?php

class Lipscore_RatingsReviews_Helper_Purchase extends Lipscore_RatingsReviews_Helper_Abstract
{    
    public function getEmail(Mage_Sales_Model_Order $order = null)
    {
        $email = null;
        
        if ($order) {
            $email = $order->getBillingAddress()->getEmail();
            if (!$email) {
                $email = $order->getCustomerEmail();
            }            
        } else {
            $customer = $this->getCustomer();
            if ($customer) {
                $email = $customer->getEmail();
            }
        }

        return $email;
    }
    
    public function getName(Mage_Sales_Model_Order $order = null)
    {
        $name = null;
        
        if ($order) {
            $addr = $order->getBillingAddress();
            $name = $addr->getFirstname() . ' ' . $addr->getLastname();
            
            if (!trim($name)) {
                $name = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
            }
        } else {
            $customer = $this->getCustomer();
            if ($customer) {
                $name = $customer->getName();
            }
        }
        
        return $name;
    }
    
    protected function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
    
    public function getWidget($type)
    {
        $layout = $this->getLayout();
        
        $layout->getUpdate()->load("checkout_{$type}_success");
        $layout->generateXml()->generateBlocks();
        
        return $layout->getBlock("lipscore.purchase.$type")->toHtml();
    }
}
