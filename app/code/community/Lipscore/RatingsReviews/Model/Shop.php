<?php

class Lipscore_RatingsReviews_Model_Shop
{
    protected $name;
    protected $url;
    protected $contactName;
    protected $contactEmail;
    protected $country;
    protected $langs;
    
    public function __construct($params)
    {
        $this->checkParameter($params, 'website');
        
        $website = $params['website'];
        
        $this->name         = $website->getName();
        $this->url          = $website->getConfig('web/unsecure/base_url');
        $this->contactName  = $website->getConfig('trans_email/ident_general/name');
        $this->contactEmail = $website->getConfig('trans_email/ident_general/email');        
        $this->country      = $website->getConfig('general/store_information/merchant_country');
        $this->langs        = $this->findLangs($website);
    }
    
    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            new Exception("Undefined property $name");
        }
    }
    
    protected function checkParameter($params, $name)
    {
        if (empty($params[$name])) {
            throw new Exception("$name parameter is empty");
        }
    }
    
    protected function findLangs($website)
    {
        $langs  = array();
        $stores = $website->getStores();
        
        foreach ($stores as $store) {
            $langs[] = $store->getConfig('general/locale/code');
        }

        return array_unique($langs);
    }
}
