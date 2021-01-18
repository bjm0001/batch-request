<?php

use qiLim\batchRequest\batchRequest;
include dirname(__DIR__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';


$data=[
    'dd'=> [
        'url'=>'http://tianqi.2345.com/tpc/searchCity.php?q=%E6%88%90%E9%83%BD&pType=pc',
        'method'=>'GET',
        'data'=>[
            'q'=>"上海",
            'pType'=>"pc",
        ]
    ],
    'dd2'=> [
        'url'=>'http://tianqi.2345.com/tpc/searchCity.php?q=%E6%88%90%E9%83%BD&pType=pc',
        'method'=>'GET',
        'data'=>[
            'q'=>"上海",
            'pType'=>"pc",
        ]
    ],
    'ee'=>[
        'url'=>'https://c.y.qq.com/mv/fcgi-bin/fcg_singer_mv.fcg?cid=205360581&singermid=001BLpXF2DyJe2&order=listen&begin=0&num=5&g_tk_new_20200303=5381&g_tk=5381&loginUin=0&hostUin=0&format=json&inCharset=utf8&outCharset=utf-8&notice=0&platform=yqq.json&needNewCode=0',
        'method'=>'get',
    ]
];

$config= [
    'data'=>$data,
    'resultType'=>"body",
    'needRecordLog'=>false,
];
$Request = new batchRequest($config);
$result=  $Request->get();
var_dump($result);

die;

$config= [
    'data'=>$data,
    'resultType'=>"body",
    'needRecordLog'=>false,
];
echo "开始时间:".date("Y-m-d H:i:s").PHP_EOL;
//单次请求最大阀值
$maximum=3;
//单次请求间隔(单位：秒)
$requestInterval=1;
$result=[];
foreach (array_chunk($data, $maximum, true) as $k => $chunk_data) {
    $config['data']=$chunk_data;
    $config['resultType']='body';
    $chunk_result = (new batchRequest($config))->get();
    $result = array_merge($result, $chunk_result);
    sleep($requestInterval);
}
print_r($result);
echo "结束时间:".date("Y-m-d H:i:s").PHP_EOL;