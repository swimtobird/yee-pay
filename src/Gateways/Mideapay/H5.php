<?php
/**
 *
 * User: swimtobird
 * Date: 2021-08-28
 * Email: <swimtobird@gmail.com>
 */

namespace Swimtobird\YeePay\Gateways\Mideapay;


use Swimtobird\YeePay\Contracts\PayGatewayInterface;

class H5 extends AbstractGateway implements PayGatewayInterface
{
    public function pay(array $params): array
    {
        $payload = [
            'partner' => $this->config->get('partner'),
            'service' => 'trade_pay_cashier',
            'version' => '3.4.0',
            'req_seq_no' => uniqid('', false),
            'input_charset' => 'UTF-8',
            'language' => 'ZH-CN',
            'terminal_type' => 'PC',
            'sign_type' => 'MD5_RSA_TW',
            'risk_params' => json_encode(['ip' => '183.27.197.35'])
        ];

        $payload = array_merge($payload, $params);

        $sign = $this->getSign($payload);

        $payload = array_merge($payload,[
            'sign' => $sign
        ]);

        $payload = http_build_query($payload);

        return ['pay_url' => $this->getHost() ."?". $payload];
    }

    public function refund(array $params): array
    {
        $payload = [
            'partner' => $this->config->get('partner'),
            'service' => 'trade_refund',
            'version' => '3.1.0',
            'req_seq_no' => uniqid('', false),
            'input_charset' => 'UTF-8',
            'language' => 'ZH-CN',
            'terminal_type' => 'PC',
            'sign_type' => 'MD5_RSA_TW',
            'risk_params' => json_encode(['ip' => '183.27.197.35'])
        ];

        $payload = array_merge($payload, $params);

        return $this->request($payload);
    }

    public function query(array $params): array
    {
        $payload = [
            'partner' => $this->config->get('partner'),
            'service' => 'trade_query',
            'version' => '3.4.0',
            'req_seq_no' => uniqid('', false),
            'input_charset' => 'UTF-8',
            'language' => 'ZH-CN',
            'terminal_type' => 'PC',
            'sign_type' => 'MD5_RSA_TW',
        ];

        $payload = array_merge($payload, $params);

        return $this->request($payload);
    }

    public function cancel(array $params): array
    {
        return [];
    }

    public function success(): string
    {
        return '';
    }
}