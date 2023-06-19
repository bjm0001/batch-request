<?php

use Bjm\Request\BatchHttp;
use Bjm\Request\Config;

include dirname(__DIR__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';


$config = new Config();
$config->url='http://tianqi.2345.com/tpc/searchCity.php?q=%E6%88%90%E9%83%BD&pType=pc';
$config->method='GET';
$config->data='data';

$config2 = new Config();
$config2->url='https://c.y.qq.com/mv/fcgi-bin/fcg_singer_mv.fcg?cid=205360581&singermid=001BLpXF2DyJe2&order=listen&begin=0&num=5&g_tk_new_20200303=5381&g_tk=5381&loginUin=0&hostUin=0&format=json&inCharset=utf8&outCharset=utf-8&notice=0&platform=yqq.json&needNewCode=0';
$config2->method='GET';

$start = time();
echo "开始时间:".date("Y-m-d H:i:s").PHP_EOL;
$batch_request = new BatchHttp([
    'data'=>[
        'case1'=> $config,
        'case2'=>$config2,
    ]
]);
$res = $batch_request->getResult();
print_r($res);
echo "结束时间:".date("Y-m-d H:i:s").PHP_EOL;