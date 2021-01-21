<?php
/**
 *
 * User: swimtobird
 * Date: 2021-01-21
 * Email: <swimtobird@gmail.com>
 */

namespace Swimtobird\YeePay\Gateways\Yee;


use Swimtobird\YeePay\Contracts\ProfitSharingGatewayInterface;

class ProfitSharing extends AbstractGateway implements ProfitSharingGatewayInterface
{
    const PROFIT_SHARING_URL = [
        'method' => 'post',
        'url' => '/rest/v1.0/divide/apply'
    ];

    const FINISH_PROFIT_SHARING_URL = [
        'method' => 'post',
        'url' => '/rest/v1.0/divide/complete'
    ];

    const QUERY_PROFIT_SHARING_URL = [
        'method' => 'get',
        'url' => '/rest/v1.0/divide/query'
    ];

    const ADD_RECEIVER_URL = [
        'method' => 'post',
        'url' => '/rest/v1.0/divide/apply'
    ];

    const REFUND_PROFIT_SHARING_URL = [
        'method' => 'post',
        'url' => '/rest/v1.0/divide/back'
    ];

    const QUERY_REFUND_PROFIT_SHARING_URL = [
        'method' => 'get',
        'url' => '/rest/v1.0/divide/back/query'
    ];

    public function profitSharing(array $params): array
    {
        return $this->request(self::PROFIT_SHARING_URL,$params);
    }

    public function queryProfitSharing(array $params): array
    {
        return $this->request(self::QUERY_PROFIT_SHARING_URL,$params);
    }

    public function addReceiver(array $params): array
    {
        return $this->request(self::ADD_RECEIVER_URL,$params);
    }

    public function removeReceiver(array $params): array
    {
        return [];
    }

    public function finishProfitSharing(array $params): array
    {
        return $this->request(self::FINISH_PROFIT_SHARING_URL,$params);
    }

    public function refundProfitSharing(array $params): array
    {
        return $this->request(self::REFUND_PROFIT_SHARING_URL,$params);
    }

    public function queryRefundProfitSharing(array $params): array
    {
        return $this->request(self::QUERY_REFUND_PROFIT_SHARING_URL,$params);
    }
}