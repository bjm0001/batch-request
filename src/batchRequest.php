<?php

namespace qiLim\batchRequest;

/**
 *
 * @method  getResultByMultiInfo
 * @method  getResultByCurlInfo
 * @method  getResultByHeaderString
 * @method  getResultByHeaderArray
 * @method  getResultByBody
 *
 * Class BatchRequest
 */
class batchRequest
{
    //curl批量句柄
    private $mh;
    //curl句柄
    private $conn;

    //请求数据
    public $data = [];
    //请求结果
    public $result = [];
    //默认请求方法
    public $defaultMethod = "GET";
    //超时时间 单位:秒
    public $timeout = 8;

    public $needRecordLog = true;

    public $logPath;

    //获取结果类型
    public $resultType = 'body';

    final private function closeHandle()
    {
        curl_multi_close($this->mh);
        return $this;
    }

    final private function checkItem(&$item)
    {
        if (empty($item['method'])) {
            $item['method'] = $this->defaultMethod;
        }
        if (empty($item['data'])) {
            $item['data'] = [];
        }
    }

    final private function createConn()
    {
        foreach ($this->data as $i => $item) {
            if (empty($item['url'])) {
                continue;
            }
            $this->conn[$i] = curl_init();
            if (empty($item['method'])) {
                $item['method'] = "GET";
            }
            $this->checkItem($item);
            $this->setOpts($i, $item);
            curl_multi_add_handle($this->mh, $this->conn[$i]);
        }
        return $this;
    }

    final private function setOpts($i, $item)
    {
        curl_setopt($this->conn[$i], CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->conn[$i], CURLOPT_SSL_VERIFYHOST, false);
        //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出
        curl_setopt($this->conn[$i], CURLOPT_RETURNTRANSFER, true);
        //设置cURL允许执行的最长秒数
        curl_setopt($this->conn[$i], CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($this->conn[$i], CURLOPT_HEADER, CURLOPT_HEADER);
        //设置请求header
        if (!empty($item['header'])) {
            curl_setopt($this->conn[$i], CURLOPT_HTTPHEADER, $item['header']);
        }
        switch (strtoupper($item['method'])) {
            case "GET":
                if (is_array($item['data'])) {
                    $data_str = '';
                    if (count($item['data']) !== 0) {
                        foreach ($item['data'] as $k => $v) {
                            $data_str .= '&' . $k . '=' . urlencode($v);
                        }
                        $data_str = substr($data_str, 1);
                    }
                } else {
                    $data_str = &$item['data'];
                }
                $url = $item['url'] . ($data_str === '' ? '' : '?' . $data_str);
                curl_setopt($this->conn[$i], CURLOPT_URL, $url);
                break;
            case "POST":
                curl_setopt($this->conn[$i], CURLOPT_URL, $item['url']);
                curl_setopt($this->conn[$i], CURLOPT_POST, true);
                curl_setopt($this->conn[$i], CURLOPT_POSTFIELDS, $item['data']);
                break;
            case "PUT":
            case "DELETE":
                curl_setopt($this->conn[$i], CURLOPT_URL, $item['url']);
                curl_setopt($this->conn[$i], CURLOPT_CUSTOMREQUEST, strtoupper($item['method']));
                curl_setopt($this->conn[$i], CURLOPT_POSTFIELDS, $item['data']);
                break;
        }
        return $this;

    }

    final private function execHandle()
    {
        $active = null;
        do {
            $mrc = curl_multi_exec($this->mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($this->mh) != -1) {
                do {
                    $mrc = curl_multi_exec($this->mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }
        foreach ($this->data as $i => $item) {
            //获取当前解析的cURL的相关传输信息
            $this->result[$i]['multiInfo'] = curl_multi_info_read($this->mh);
            //获取请求头信息
            $this->result[$i]['curlInfo'] = curl_getinfo($this->conn[$i]);
            //获取输出的文本流
            $headerString = $headerArray = $body = [];
            $response = curl_multi_getcontent($this->conn[$i]);

            $headerSize = curl_getinfo($this->conn[$i], CURLINFO_HEADER_SIZE);
            $headerString = substr($response, 0, $headerSize);
            $headerArray = $this->analysisHeader($headerString);
            $body = substr($response, $headerSize);

            $this->result[$i]['headerString'] = $headerString;
            $this->result[$i]['headerArray'] = $headerArray;
            $this->result[$i]['body'] = $body;
            // 移除curl批处理句柄资源中的某个句柄资源
            curl_multi_remove_handle($this->mh, $this->conn[$i]);
            //关闭cURL会话
            curl_close($this->conn[$i]);
        }
        return $this;
    }

    final private function analysisHeader($str)
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

    public function __construct($option = [])
    {
        if (!empty($option['data'])) {
            $this->data = $option['data'];
        }
        if (!empty($option['defaultMethod']) && in_array($option['defaultMethod'], $this->allowMethod())) {
            $this->defaultMethod = $option['defaultMethod'];
        }
        if (!empty($option['timeout'])) {
            $this->data = $option['timeout'];
        }
        if (isset($option['needRecordLog'])&&is_bool($option['needRecordLog'])) {
            $this->needRecordLog = $option['needRecordLog'];
        }

        if (!empty($option['resultType'])) {
            $this->resultType = $option['resultType'];
        }
        $this->logPath = (!empty($option['logPath'])) ? $option['logPath'] : dirname(__DIR__) . DIRECTORY_SEPARATOR . "logs/" . date("Y-m-d") . ".log";
        $this->mh = curl_multi_init();
    }

    public function allowMethod()
    {
        return ["GET", "POST", "PUT", "DELETE"];
    }

    public function setData($data = [])
    {
        $this->data = $data;
        return $this;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function __call($name, $arguments)
    {
        $tmp = explode('getResultBy', $name);
        if (count($tmp) == 2) {
            $result = [];
            foreach ($this->result as $k => $item) {
                $result[$k] = isset($item[lcfirst($tmp[1])]) ? $item[lcfirst($tmp[1])] : null;
            }
            return $result;
        }
    }

    public function get()
    {
        if (empty($this->data)) {
            return $this->closeHandle();
        }
        $actionName = ($this->resultType == "result") ? 'getResult' : 'getResultBy' . ucfirst($this->resultType);
        $this->writeLog($this->data,"START");
        $this->createConn();
        $this->execHandle();
        $this->closeHandle();
        $this->writeLog($this->getResultByBody(),'END');
        return $this->$actionName();
    }


    public function getLogPath()
    {
        return $this->logPath;
    }

    private function writeLog($msg,$action="default")
    {
        if (!$this->needRecordLog) {
            return false;
        }
        if (!is_dir(dirname($this->logPath))) {
            mkdir(dirname($this->logPath), 0777, true);
        }
        if ($msg) {
            if (!$msgS = json_encode($msg, JSON_UNESCAPED_UNICODE)) {
                $msgS = "json解析错误，code:" . json_last_error() . "msg:" . json_last_error_msg();
            };
            $msg = "[" . date("Y-m-d H:i:s") . "] :[{$action}]: $msgS" . PHP_EOL;
            file_put_contents($this->logPath, $msg, FILE_APPEND);
        }
    }
}