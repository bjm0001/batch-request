## 对象初始化配置
| 请求参数 | 默认值 | 备注                                                         |
| -------- | ------ | ------------------------------------------------------------ |
| data     | []     | 请求数据                                                     |
| log_path   | /tmp/batch_request.log  | 日志路径，目前未用到|
| need_record_log   | false | 是否需要日志，目前未用到|


## data参数介绍
data参数采用Config对象构建

| 请求参数 | 默认值 | 备注                                                         |
| -------- | ------ | ------------------------------------------------------------ |
| url     | ''    | 请求地址                                                     |
| method   | GET  | 请求方法|
| header   | array | 请求header|
| timeout   | 1 | 超时时间|
| response_json_decode   | false | 返回结果是否需要进行json解码|
| debug   | false | 是否需要调试，返回curl info 跟header信息|
| data   | null | 请求数据|




## 请求示例：
```php
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
];

$start = time();
echo "开始时间:".date("Y-m-d H:i:s").PHP_EOL;
$batch_request = new batchRequest(['data'=>$data]);
$res = $batch_request->getResult();
print_r($res);
echo "结束时间:".date("Y-m-d H:i:s").PHP_EOL;
```
## 返回示例：
```
开始时间:2023-06-16 12:31:27
Array
(
    [case1] => Array
        (
            [code] => 200
            [body] => {"res":[{"href":"\/pc\/pcIndex\/chengdu\/56294","text":"\u6210\u90fd\u5e02 - <span>\u56db\u5ddd<\/span>"}]}
        )

    [case2] => Array
        (
            [code] => 200
            [body] => {"code":0,"subcode":0,"message":"succ","data":{"list":[{"index":1,"vid":"d0015z6s2c7","id":"303941","title":"可惜没如果 (剧情版)","desc":"","pic":"httimg.cn/music/photo_new/T015R640x360M1010034XEI80uvuG3.jpg","encrypt_uin":"on**","upload_uin":"0","upload_nick":"","upload_pic":"","score":"0","listenCount":"52984209","date":"2014-12-19","singer_id":4286,"singer_name":"林俊杰","singer_mid":"001BLpXF2DyJe2"},{"index":2,"vid":"g0011qp6w2g","id":"193007","title":"修炼爱情","desc":"","pic":"http:mg.cn/music/photo_new/T015R640x360M101003xAd6w3dAlI5.jpg","encrypt_uin":"on**","upload_uin":"0","upload_nick":"","upload_pic":"","score":"0","listenCount":"36218327","date":"2013-03-01","singer_id":4286,"singer_name":"林俊杰","singer_mid":"001BLpXF2DyJe2"},{"index":3,"vid":"o0024afmmj5","id":"56627","title":"醉赤壁 (《赤壁Online》网游主题曲)"":"http://y.gtimg.cn/music/photo_new/T015R640x360M102001LzHpF448r1N.jpg","encrypt_uin":"on**","upload_uin":"0","upload_nick":"","upload_pic":"","score":"0","listenCount":"12908943","date":"2010-07-29","singer_id":4286,"singer_name":"林俊杰","singer_mid":"001BLpXF2DyJe2"},{"index":4,"vid":"n0030ls9lm6","id":"192210","title":"因你而在","desc":":"http://y.gtimg.cn/music/photo_new/T015R640x360M101002LiGei3YQvWh.jpg","encrypt_uin":"on**","upload_uin":"0","upload_nick":"","upload_pic":"","score":"0","listenCount":"8270668","date":"2013-07-13","singer_id":4286,"singer_name":"林俊杰","singer_mid":"001BLpXF2DyJe2"},{"index":5,"vid":"z00220ah8mt","id":"661118","title":"不为谁而作的歌","des":"http://y.gtimg.cn/music/photo_new/T015R640x360M1010001cwTR3WdIfm.jpg","encrypt_uin":"on**","upload_uin":"0","upload_nick":"","upload_pic":"","score":"0","listenCount":"36522468","date":"2016-02-05","singer_id":4286,"singer_name":"林俊杰","singer_mid":"001BLpXF2DyJe2"}],"total":584}}

        )
)
结束时间:2023-06-16 12:31:28
```


