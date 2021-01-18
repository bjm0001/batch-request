## 对象初始化配置
| 请求参数 | 默认值 | 备注                                                         |
| -------- | ------ | ------------------------------------------------------------ |
| data     | []     | 请求数据                                                     |
| defaultMethod  | GET    | 默认请求方法                                           |
| timeout   | 8   | 默认超时时间(单位：秒) |
| needRecordLog   | true   | 是否需要日志记录(boolean类型)|
| logPath   | ../log/Y-m-d.log   | 日志路径|
| resultType   | body  | 获取结果类型，支持[MultiInfo，CurlInfo，HeaderString，HeaderArray，Body]|

## 请求示例：
```php
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
echo "开始时间:".date("Y-m-d H:i:s").PHP_EOL;
$Request = new batchRequest($config);
$result=  $Request->get();
var_dump($result);
echo "结束时间:".date("Y-m-d H:i:s").PHP_EOL;

```
## 返回示例：
```
array(
 'dd'=>'xxxxxxx',
 'dd2'=>'xxxxxxx',
 'ee'=>'xxxxxxx',
)

```

> 有时候请求数量量比较大，而服务端接口承受不住那么高的迸发，可以选择分批次请求

```php
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
```
## 返回示例：
```
array(
 'dd'=>'xxxxxxx',
 'dd2'=>'xxxxxxx',
 'ee'=>'xxxxxxx',
)

```



