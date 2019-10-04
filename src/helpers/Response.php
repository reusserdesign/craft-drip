<?php

namespace extreme\drip\helpers;

class Response
{
    public $status  = null;
    public $error   = null;
    public $message = null;

    protected $data = [];

    public function __construct($meta, $body)
    {
        $this->processMeta($meta);
        $this->processBody($body);
        $this->handleErrors();
    }

    /**
     * @param $meta
     */
    protected function processMeta($meta)
    {
        if (isset($meta['http_code'])) {
            $this->status = (int) $meta['http_code'];
        }
    }

    protected function processBody($body)
    {
        $decoded_body = json_decode($body, true);
        if (is_array($decoded_body)) {
            $this->data = $decoded_body;
        }
    }

    protected function handleErrors()
    {
        if (is_array($this->data) && isset($this->data['errors'])) {
            $this->error   = $this->data['errors'][0]['code'];
            $this->message = $this->data['errors'][0]['message'];
        }
    }

    public function __get($name)
    {
        if (is_array($this->data) && isset($this->data[$name])) {
            return $this->data[$name];
        }

        return false;
    }

    public function get()
    {
        return $this->data;
    }

    public function __toString()
    {
        return print_r($this->data, true);
    }

    public function __debugInfo()
    {
        return $this->data;
    }
}