<?php

namespace App\Zhihu\Bridge;

use GuzzleHttp\Client;

/**
 * 自己封装的 GuzzleHttp\Client
 */
class Http
{
    /**
     * 请求的地址
     */
    protected $uri;

    /**
     * 请求的方法
     */
    protected $method;

    /**
     * 请求头
     */
    protected $header;

    /**
     * 请求体
     */
    protected $body;

    /**
     * 查询字符串
     */
    protected $query;

    /**
     * 设置 form params
     */
    protected $formParams;

    /**
     * ssl cert
     */
    protected $sslCert;

    /**
     * ssl key
     */
    protected $sslKey;

    /**
     * 构造方法
     */
    public function __construct($method, $uri)
    {
        $this->method       = $method;
        $this->uri          = $uri;
        $this->query        = array();
        $this->header       = array();
        $this->formParams   = array();
    }

    /**
     * 创建请求
     */
    public static function request($method, $uri)
    {
        return new static($method, $uri);
    }

    /**
     * 设置请求头
     */
    public function withHeader($key, $value = '')
    {
        if (is_array($key)) {
            $this->header = array_merge($this->header, $key);
        } else {
            $this->header[$key] = $value;
        }

        return $this;
    }

    /**
     * 设置查询字符串
     */
    public function withQuery($key, $value = '')
    {
        if (is_array($key)) {
            $this->query = array_merge($this->query, $key);
        } else {
            $this->query[$key] = $value;
        }

        return $this;
    }

    /**
     * 设置请求体
     */
    public function withBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * 用来发送一个 application/x-www-form-urlencoded POST请求.
     */
    public function withFormParams($key, $value = '')
    {
        if (is_array($key)) {
            $this->formParams = array_merge($this->formParams, $key);
        } else {
            $this->formParams[$key] = $value;
        }

        return $this;
    }

    /**
     * 设置请求秘钥文件
     */
    public function withSSLCert($sslCert, $sslKey)
    {
        $this->sslCert  = $sslCert;
        $this->sslKey   = $sslKey;

        return $this;
    }

    /**
     * 发送请求
     */
    public function send($isArray = true)
    {
        $options = array();

        if (!empty($this->query)) {
            $options['query'] = $this->query;
        }

        if (!empty($this->formParams)) {
            $options['form_params'] = $this->formParams;
        }

        if (!empty($this->body)) {
            $options['body'] = $this->body;
        }

        if ($this->sslCert && $this->sslKey) {
            $options['cert']    = $this->sslCert;
            $options['ssl_key'] = $this->sslKey;
        }

        $response = (new Client())->request($this->method, $this->uri, $options);
        $contents = $response->getBody()->getContents();

        if (!$isArray) {
            return $contents;
        }

        return Serializer::parse($contents);
    }
}
