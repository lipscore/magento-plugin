<?php

/**
* Init helper
*
* @author oivanova
*/

class Lipscore_RatingsReviews_Helper_Init extends Lipscore_RatingsReviews_Helper_Abstract
{
    function getLipscoreApiKey()
    {
        return $this->_lipscoreConfig->get('api_key', 'apiKey');
    }
}

