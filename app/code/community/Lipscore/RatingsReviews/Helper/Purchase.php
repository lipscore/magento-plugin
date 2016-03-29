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
        }

        return $name;
    }
}
