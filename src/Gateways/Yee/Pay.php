<?php
/**
 *
 * User: swimtobird
 * Date: 2021-01-19
 * Email: <swimtobird@gmail.com>
 */

namespace Swimtobird\YeePay\Gateways\Yee;


class Pay extends AbstractGateway
{
    const PAY_URL = [
        'method' => 'post',
        'url' => '/rest/v1.0/aggpay/pre-pay'
    ];

    const CANCAL_URL = [
        'method' => 'post',
        'url' => '/rest/v1.0/trade/order/close'
    ];

    const QUERY_URL = [
        'method' => 'post',
        'url' => '/rest/v1.0/trade/order/query'
    ];

    const REFUND_URL = [
        'method' => 'post',
        'url' => '/rest/v1.0/trade/refund'
    ];

    /**
     * @param array $params
     * @return array
     */
    public function pay(array $params): array
    {
        return $this->request(self::PAY_URL, $params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function cancel(array $params): array
    {
        return $this->request(self::CANCAL_URL,$params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function query(array $params): array
    {
        return $this->request(self::QUERY_URL,$params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function refund(array $params): array
    {
        return $this->request(self::REFUND_URL,$params);
    }

    /**
     * @return string
     */
    public function success(): string
    {
        return 'SUCCESS';
    }
}