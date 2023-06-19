<?php

namespace Bjm\Request;

class BatchHttp
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

    public function send(): array
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
            curl_setopt_array($ch, $config->custom_curl_option);
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
        $responses = $this->send();
        foreach ($responses as $key => $response) {
            /**
             * @var Response $response
             */
            $result[$key] = $response->getResult();
        }
        return $result;
    }

}