<?php
/**
 *
 * User: swimtobird
 * Date: 2021-01-19
 * Email: <swimtobird@gmail.com>
 */

namespace Swimtobird\YeePay\Contracts;


interface PayGatewayInterface
{
    /**
     * 发起支付
     * @param array $params
     * @return mixed
     */
    public function pay(array $params): array;

    /**
     * 退款
     * @param array $params
     * @return mixed
     */
    public function refund(array $params): array;

    /**
     * 订单查询
     * @param array $params
     * @return mixed
     */
    public function query(array $params): array;

    /**
     * 订单取消
     * @param array $parms
     * @return mixed
     */
    public function cancel(array $params): array;

    /**
     * 通知成功处理事件
     * @return mixed
     */
    public function success(): string;
}