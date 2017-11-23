<?php

class Lipscore_RatingsReviews_Model_Api_Request
{
    protected $lipscoreConfig;
    protected $path;
    protected $requestType;
    protected $timeout = 10;
    protected $response;

    public function __construct($params)
    {
        $this->checkParameter($params, 'lipscoreConfig');
        $this->checkParameter($params, 'path');

        $this->lipscoreConfig = $params['lipscoreConfig'];
        $this->path           = $params['path'];

        if (!empty($params['timeout'])) {
            $this->timeout = $params['timeout'];
        }

        if (!empty($params['requestType'])) {
            $this->requestType = $params['requestType'];
        }
    }

    public function getResponseMsg()
    {
        return $this->response ? $this->response->__toString() : '';
    }

    public function send($data)
    {
        $apiKey = $this->lipscoreConfig->apiKey();
        $secret = $this->lipscoreConfig->secret();
        $apiUrl = Mage::getModel('lipscore_ratingsreviews/config_env')->apiUrl();

        $client = new Zend_Http_Client(
            "http://$apiUrl/{$this->path}?api_key=$apiKey", array('timeout' => $this->timeout)
        );
        $client->setRawData(json_encode($data), 'application/json')
               ->setMethod($this->getRequestType())
               ->setHeaders('X-Authorization', $secret);

        $this->response = $client->request();

        $result = $this->response->isSuccessful();
        if ($result) {
            $result = json_decode($this->response->getBody(), true);
        }

        return $result;
    }

    protected function checkParameter($params, $name)
    {
        if (empty($params[$name])) {
            throw new Exception("$name parameter is empty");
        }
    }

    protected function getRequestType()
    {
        return $this->requestType ? $this->requestType : Zend_Http_Client::POST;
    }
}
