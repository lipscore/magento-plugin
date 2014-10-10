<?php

class Lipscore_RatingsReviews_Helper_Purchase extends Lipscore_RatingsReviews_Helper_Abstract
{    
    function getEmail(Mage_Sales_Model_Order $order = null)
    {
        $email = null;
        
        if ($order) {
            $email = $order->getBillingAddress()->getEmail();
            if (!$email) {
                $email = $order->getCustomerEmail();
            }            
        } else {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if ($customer) {
                $email = $customer->getEmail();
            }
        }

        return $email;
    }
}
