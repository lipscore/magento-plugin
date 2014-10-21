<?php

class Lipscore_RatingsReviews_Block_Review_Helper extends Mage_Review_Block_Helper
{
    protected $_template = 'lipscore/rating/view.phtml';
    protected static $_availableTypes = array(
        'long'  => 'id="lipscore-rating"',
        'short' => 'class="lipscore-rating-small"'
    );
    
    public function getSummaryHtml($product, $templateType, $displayIfNoReviews)
    {
        empty(self::$_availableTypes[$templateType]) and $templateType = 'short';
    
        $this->setRatingType(self::$_availableTypes[$templateType]);
        $this->setProduct($product);
    
        return $this->toHtml();
    }
}
