## 对象初始化配置
| 请求参数 | 默认值 | 备注                                                         |
| -------- | ------ | ------------------------------------------------------------ |
| data     | []     | 请求数据                                                     |
| defaultMethod  | GET    | 默认请求方法                                           |
| timeout   | 8   | 默认超时时间(单位：秒) |
| needRecordLog   | true   | 是否需要日志记录(boolean类型)|
| logPath   | ../log/Y-m-d.log   | 日志路径|

## 构造请求
```php
$data=[
   'dd'=> [
        'url'=>'http://xxxxxxx',
        'method'=>'GET',
        'header'=>[
            'cookie:xxxxxxxx'
        ]
    ],
    'ee'=>[
        'url'=>'http://xxxxxxx',
        'method'=>'post',
        'data'=>['key'=>'value'],
    ]
];
$result=  $Request->setData($data)->run()->getResultByBody();
```
## 返回示例：
```
array(
 'dd'=>'xxxxxxx',
 'ee'=>'xxxxxxx',
)

```

> 有时候请求数量量比较大，而服务端接口承受不住那么高的迸发，可以选择分批次请求

```php

$result=BatchRequest::batchRun($data,100,'body');
```
| 请求参数 | 默认值 | 备注                                                         |
| -------- | ------ | ------------------------------------------------------------ |
| data     | []     | 请求数据                                                     |
| maximum  | 100    | 单次请求的最大数量                                           |
| action   | Body   | 获取body结果,支持[MultiInfo，CurlInfo，HeaderString，HeaderArray，Body] |




