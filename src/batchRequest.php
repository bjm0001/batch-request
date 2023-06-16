<?php

namespace Bjm\batchRequest;

class batchRequest
{
    public $request_data = [];

    public $log_path = '/tmp/batch_request.log';

    public $need_record_log = false;

    public function __construct($option = [])
    {
        if (isset($option['log_path']) && is_string($option['log_path'])) {
            $this->log_path = $option['log_path'];
        }
        if (isset($option['data']) && is_array($option['data'])) {
            foreach ($option['data'] as $key => $item) {
                if (is_array($item)) {
                    $config = Config::getInstance($item);
                    if ($config) {
                        $this->request_data[$key] = $config;
                        continue;
                    }
                }
                if ($item instanceof Config) {
                    $this->request_data[$key] = $item;
                }
            }
        }
    }

    public function writeLog(string $level, string $error_message, $context = []): bool
    {
        if (!$this->need_record_log) {
            return false;
        }
        $condition = !is_dir(dirname($this->log_path)) &&
            !mkdir($concurrentDirectory = dirname($this->log_path), 0777, true) &&
            !is_dir($concurrentDirectory);

        if ($condition) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        $msg = sprintf("%s\t%s\t%s", $level, $error_message, json_encode($context));
        file_put_contents($this->log_path, $msg, FILE_APPEND);
        return true;
    }

    private function analysisHeader($str): array
    {
        $tmp = explode(PHP_EOL, $str);
        $header = [];
        foreach ($tmp as $item) {
            if (empty($item)) {
                continue;
            }
            if (strstr($item, ':') !== false) {
                list($key, $value) = explode(': ', $item);
                $header[$key] = str_replace(["\n", "\r"], ['', ''], $value);
            }
        }
        return $header;
    }

    public function request(): array
    {
        $mh = curl_multi_init();
        $all_curl = [];
        foreach ($this->request_data as $key => $config) {
            /**
             * @var  Config $config
             */
            if (!$config->url) {
                continue;
            }
            $ch = curl_init();
            $options = [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $config->timeout,
                //返回结果中是否包含header
                CURLOPT_HEADER => true,
                //设置请求header
                CURLOPT_HTTPHEADER => $config->header,
            ];
            switch (strtoupper($config->method)) {
                case "GET":
                    $data_str = is_array($config->data) ? http_build_query($config->data) : $config->data;
                    $url = $config->url . ($data_str === '' ? '' : '?' . $data_str);
                    $options[CURLOPT_URL] = $url;
                    break;
                case "POST":
                    $options[CURLOPT_URL] = $config->url;
                    $options[CURLOPT_POST] = true;
                    $options[CURLOPT_POSTFIELDS] = $config->data;
                    break;
                case "PUT":
                case "DELETE":
                    $options[CURLOPT_URL] = $config->url;
                    $options[CURLOPT_CUSTOMREQUEST] = strtoupper($config->method);
                    $options[CURLOPT_POSTFIELDS] = $config->data;
                    break;
            }
            curl_setopt_array($ch, $options);
            $all_curl[$key] = $ch;
            curl_multi_add_handle($mh, $ch);
        }
        // 执行批处理句柄
        do {
            $status = curl_multi_exec($mh, $active);
            if ($active) {
                curl_multi_select($mh);
            }
        } while ($active && $status === CURLM_OK);

        $result = [];
        foreach ($all_curl as $key => $curl) {
            $result[$key] = Response::getInstance([
                'multi_info' => curl_multi_info_read($mh),
                'curl_info' => curl_getinfo($curl),
                'content' => curl_multi_getcontent($curl),
                'header_size' => curl_getinfo($curl, CURLINFO_HEADER_SIZE),
                'code' => curl_getinfo($curl, CURLINFO_HTTP_CODE),
                'config'=>$this->request_data[$key]
            ]);
            // 移除curl批处理句柄资源中的某个句柄资源
            curl_multi_remove_handle($mh, $curl);
            //关闭cURL会话
            curl_close($curl);
        }
        curl_multi_close($mh);
        return $result;
    }

    public function getResult(): array
    {
        $result = [];
        $responses = $this->request();
        foreach ($responses as $key => $response) {
            /**
             * @var Response $response
             */
            $result[$key] = $response->getResult();
        }
        return $result;
    }

}