<?php
/**
 *
 * User: swimtobird
 * Date: 2021-01-19
 * Email: <swimtobird@gmail.com>
 */

namespace Swimtobird\YeePay\Gateways\Yee;

use Swimtobird\YeePay\Contracts\PayGatewayInterface;

class Pay extends AbstractGateway implements PayGatewayInterface
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
        $payload = [
            'parentMerchantNo' => $this->config->get('parent_merchant_no'),
            'merchantNo' => $this->config->get('merchant_no'),
        ];

        $payload = array_merge($payload, $params);

        return $this->request(self::PAY_URL, $payload);
    }

    /**
     * @param array $params
     * @return array
     */
    public function cancel(array $params): array
    {
        $payload = [
            'parentMerchantNo' => $this->config->get('parent_merchant_no'),
            'merchantNo' => $this->config->get('merchant_no'),
        ];

        $payload = array_merge($payload, $params);

        return $this->request(self::CANCAL_URL,$payload);
    }

    /**
     * @param array $params
     * @return array
     */
    public function query(array $params): array
    {
        $payload = [
            'parentMerchantNo' => $this->config->get('parent_merchant_no'),
            'merchantNo' => $this->config->get('merchant_no'),
        ];

        $payload = array_merge($payload, $params);

        return $this->request(self::QUERY_URL,$payload);
    }

    /**
     * @param array $params
     * @return array
     */
    public function refund(array $params): array
    {
        $payload = [
            'parentMerchantNo' => $this->config->get('parent_merchant_no'),
            'merchantNo' => $this->config->get('merchant_no'),
        ];

        $payload = array_merge($payload, $params);

        return $this->request(self::REFUND_URL,$payload);
    }

    /**
     * @return string
     */
    public function success(): string
    {
        return 'SUCCESS';
    }
}