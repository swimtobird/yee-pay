# yee-pay

## 目前支持支付网关
| 名称 | 网关名称 |
| :--- | :---- |
| Yee_Pay | 易宝聚合支付 |

## 目前支持分账网关
| 名称 | 网关名称 |
| :--- | :---- |
| Yee_ProfitSharing | 易宝分账 |

## 支付网关目前支持以下方法
- pay(array $params)  
说明：发起支付接口  

- query(array $params)  
说明：查找订单接口  

- refund(array $params)  
说明：退款接口  

- cancel(array $params)  
说明：取消订单接口

## 分账网关目前支持以下方法
- profitSharing(array $params)  
说明：申请分账  

- queryProfitSharing(array $params)  
说明：查询分账结果  

- addReceiver(array $params)  
说明：添加分账接收方  

- removeReceiver(array $params)  
说明：移除分账接收方

- finishProfitSharing(array $params)  
说明：完结分账

- refundProfitSharing(array $params)  
说明：申请分账回退

- refundProfitSharing(array $params)  
说明：查询分账回退结果

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
