<?php

use qiLim\batchRequest\batchRequest;
include dirname(__DIR__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';


$Request = new batchRequest();
$data=[
    'dd'=> [
        'url'=>'http://tianqi.2345.com/tpc/searchCity.php?q=%E6%88%90%E9%83%BD&pType=pc',
        'method'=>'GET',
        'data'=>[
            'q'=>"成都",
            'pType'=>"pc",
        ]
    ],
    'ee'=>[
        'url'=>'https://c.y.qq.com/mv/fcgi-bin/fcg_singer_mv.fcg?cid=205360581&singermid=001BLpXF2DyJe2&order=listen&begin=0&num=5&g_tk_new_20200303=5381&g_tk=5381&loginUin=0&hostUin=0&format=json&inCharset=utf8&outCharset=utf-8&notice=0&platform=yqq.json&needNewCode=0',
        'method'=>'get',
    ]
];

$result=BatchRequest::batchRun($data,1,'header');
//$result=  $Request->setData($data)->run()->getResultByBody();
var_dump($result);