<?php

use Bjm\batchRequest\batchRequest;
include dirname(__DIR__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
$data=[
    'case1'=> [
        'url'=>'http://tianqi.2345.com/tpc/searchCity.php?q=%E6%88%90%E9%83%BD&pType=pc',
        'method'=>'GET',
//        'response_json_decode'=>true,
        'data'=>[
            'q'=>"上海",
            'pType'=>"pc",
        ]
    ],
    'case2'=>[
        'url'=>'https://c.y.qq.com/mv/fcgi-bin/fcg_singer_mv.fcg?cid=205360581&singermid=001BLpXF2DyJe2&order=listen&begin=0&num=5&g_tk_new_20200303=5381&g_tk=5381&loginUin=0&hostUin=0&format=json&inCharset=utf8&outCharset=utf-8&notice=0&platform=yqq.json&needNewCode=0',
        'method'=>'get',
    ],
    'case3'=>[
        'url'=>'https://114.55.32.202:13456/apim/v1/homepage/customPairsGetGroup',
        'method'=>'post',
        'data'=>[
            "Timestamp" => "1669268849",
            "android_build" => "211",
            "android_google_appstore" => "0",
            "android_version" => "3.10.5",
            "appKey" => "1C843F4B-C351-4A9F-EB51-B722122341D5",
            "appLang" => "cn",
            "ch" => "",
            "device_id" => "120c83f760363f54fc7",
            "device_name" => "HUAWEI HMA-AL00",
            "night" => "0",
            "token" => "679001229_2ba986431212ac273c845a4cd334a00f",
            "sign" => "004964c83482dd58328da1b34e462b33",
        ]
    ],
    'case4'=>[
        'url'=>'https://114.55.32.202:13456/internalapi/3/user_trade_info',
        'method'=>'post',
//        'response_json_decode'=>true,
        'header'=>[
            "x-gate-userid:987",
            "x-gate-market:ETH_USDT",
            "x-gate-channel-id:hummingbot",
            "x-gate-settle:USDT",
        ]
    ],
];

$start = time();
echo "开始时间:".date("Y-m-d H:i:s").PHP_EOL;
$batch_request = new batchRequest(['data'=>$data]);
$res = $batch_request->getResult();
print_r($res);
echo "结束时间:".date("Y-m-d H:i:s").PHP_EOL;