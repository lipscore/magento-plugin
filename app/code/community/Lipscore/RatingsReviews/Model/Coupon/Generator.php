<?php

class Lipscore_RatingsReviews_Model_Coupon_Generator
{
    public function generate($rule, $data)
    {
        $generator = $rule->getCouponMassGenerator();
        
        if (!$generator->validateData($data)) {
            return null;    
        }

        $generator->setData($data);
        
        $maxProbability = $generator->getMaxProbability() ? $generator->getMaxProbability() : $generator::MAX_PROBABILITY_OF_GUESSING;
        $maxAttempts = $generator->getMaxAttempts() ? $generator->getMaxAttempts() : $generator::MAX_GENERATE_ATTEMPTS;
        
        $coupon = Mage::getModel('salesrule/coupon');
        
        $chars = count(Mage::helper('salesrule/coupon')->getCharset($generator->getFormat()));
        $length = (int) $generator->getLength();
        $maxCodes = pow($chars, $length);
        $probability = $size / $maxCodes;
        
        //increase the length of Code if probability is low
        if ($probability > $maxProbability) {
            do {
                $length++;
                $maxCodes = pow($chars, $length);
                $probability = $size / $maxCodes;
            } while ($probability > $maxProbability);
            $generator->setLength($length);
        }
        
        $now = $generator->getResource()->formatDate(
            Mage::getSingleton('core/date')->gmtTimestamp()
        );
        
        $attempt = 0;
        do {
            if ($attempt >= $maxAttempts) {
                break;
            }
            $code = $generator->generateCode();
            $attempt++;
        } while ($generator->getResource()->exists($code));
        
        $expirationDate = $generator->getToDate();
        if ($expirationDate instanceof Zend_Date) {
            $expirationDate = $expirationDate->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        }
        
        $coupon->setId(null)
            ->setRuleId($generator->getRuleId())
            ->setUsageLimit($generator->getUsesPerCoupon())
            ->setUsagePerCustomer($generator->getUsesPerCustomer())
            ->setExpirationDate($expirationDate)
            ->setCreatedAt($now)
            ->setType(Mage_SalesRule_Helper_Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED)
            ->setCode($code)
            ->save();
        
        return $coupon;
    }
}
