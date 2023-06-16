<?php

namespace Bjm\batchRequest;


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
        return $self;
    }
}

