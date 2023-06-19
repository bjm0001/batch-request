<?php

namespace Bjm\Request;

use ReflectionProperty;

class Response
{
    // 获取当前传输的有关信息
    public $multi_info = [];
    // 获取请求头信息
    public $curl_info = [];
    // 获取输出的文本流
    public $content = '';

    public $code = 0;

    public $header_size = 0;

    public $header = [];

    public $body = [];

    /**
     * @var Config $config
     */
    public $config;

    public static function getInstance($option = []): Response
    {
        $self = new self();
        $ref = new \ReflectionClass(self::class);
        $props = $ref->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        foreach ($props as $prop) {
            $name = $prop->getName();
            if (isset($option[$name])) {
                $self->{$name} = $option[$name];
            }
        }
        $headerString = substr($self->content, 0, $self->header_size);
        $self->header = $self->analysisHeader($headerString);
        $self->body = substr($self->content, $self->header_size);
        return $self;
    }

    private function analysisHeader($str): array
    {
        $tmp = explode(PHP_EOL, $str);
        $header = [];
        foreach ($tmp as $item) {
            if (empty($item)) {
                continue;
            }
            if (strpos($item, ':') !== false) {
                list($key, $value) = explode(': ', $item);
                $header[$key] = str_replace(["\n", "\r"], ['', ''], $value);
            }
        }
        return $header;
    }

    public function getResult(): array
    {
        $response = [
            'code' => $this->code,
            'body' => $this->config->response_json_decode ? json_decode($this->body, true) : $this->body,
        ];
        if ($this->config->debug) {
            $response['curl_info'] = $this->curl_info;
            $response['header'] = $this->header;
        }
        return $response;
    }

}