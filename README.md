# yee-pay

## 目前支持网关
| 名称 | 网关 |
| :--- | :---- |
| Yee_Pay | 易宝聚合支付 |

## 支持的方法
所有网关均支持以下方法
- pay(array $params)  
说明：发起支付接口  

- query(array $params)  
说明：查找订单接口  

- refund(array $params)  
说明：退款接口  

- cancel(array $params)  
说明：取消订单接口


### 使用
以易宝支付为列
```php
<?php

$config = [
    'app_key' => 'xxx',
    'merchant_no' => 'xxx',
    'parent_merchant_no' => 'xxx',
    'public_key' => 'xxx',
    'app_private_key' => 'xxx'
];

$pay = new Swimtobird\YeePay\PayProvider('Yee_Pay',$config);

var_dump($pay->pay([
    'merchantNo' => 10000466938,
    'parentMerchantNo' => 10000466938,
]));
```
