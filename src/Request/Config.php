<?php

namespace Bjm\Request;


use ReflectionProperty;

class Config
{
    /**
     * @var string $url 请求地址
     */
    public $url = '';

    /**
     * @var string $method 请求方法
     */
    public $method = 'GET';

    /**
     * @var array $header 请求头
     */
    public $header = [];

    /**
     * @var int $timeout 请求超时时间
     */
    public $timeout = 1;

    /**
     * @var bool
     */
    public $response_json_decode = false;

    /**
     * @var bool
     */
    public $debug = false;

    /**
     * @var null $data 请求数据
     */
    public $data = null;

    /**
     * @var array  curl自定义配置
     */
    public $custom_curl_option = [];


    public static function getInstance($option = []): Config
    {
        $self = new  self();
        $ref = new \ReflectionClass(self::class);
        $props = $ref->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        foreach ($props as $prop) {
            $name = $prop->getName();
            if (isset($option[$name])) {
                $self->{$name} = $option[$name];
            }
        }
        $self->setDefaultCurlOption();
        return $self;
    }

    public function setDefaultCurlOption(): bool
    {
        if (empty($this->custom_curl_option) && $this->method && $this->url && $this->timeout) {
            $custom_curl_option = [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $this->timeout,
                //返回结果中是否包含header
                CURLOPT_HEADER => true,
                //设置请求header
                CURLOPT_HTTPHEADER => $this->header,
            ];
            switch (strtoupper($this->method)) {
                case "GET":
                    $data_str = is_array($this->data) ? http_build_query($this->data) : $this->data;
                    $url = $this->url . ($data_str === '' ? '' : '?' . $data_str);
                    $custom_curl_option[CURLOPT_URL] = $url;
                    break;
                case "POST":
                    $custom_curl_option[CURLOPT_URL] = $this->url;
                    $custom_curl_option[CURLOPT_POST] = true;
                    $custom_curl_option[CURLOPT_POSTFIELDS] = $this->data;
                    break;
                case "PUT":
                case "DELETE":
                    $custom_curl_option[CURLOPT_URL] = $this->url;
                    $custom_curl_option[CURLOPT_CUSTOMREQUEST] = strtoupper($this->method);
                    $custom_curl_option[CURLOPT_POSTFIELDS] = $this->data;
                    break;
            }

            $this->custom_curl_option = $custom_curl_option;
            return true;
        }
        return false;
    }
}

