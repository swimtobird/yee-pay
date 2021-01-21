<?php
/**
 *
 * User: swimtobird
 * Date: 2021-01-21
 * Email: <swimtobird@gmail.com>
 */

namespace Swimtobird\YeePay\Contracts;


interface ProfitSharingGatewayInterface
{
    /**
     * 申请分账
     * @param array $params
     * @return array
     */
    public function profitSharing(array $params): array;

    /**
     * 查询分账结果
     * @param array $params
     * @return array
     */
    public function queryProfitSharing(array $params): array;

    /**
     * 添加分账接收方
     * @param array $params
     * @return array
     */
    public function addReceiver(array $params): array;

    /**
     * 移除分账接收方
     * @param array $params
     * @return array
     */
    public function removeReceiver(array $params): array;

    /**
     * 完结分账
     * @param array $params
     * @return array
     */
    public function finishProfitSharing(array $params): array;

    /**
     * 申请分账回退
     * @param array $params
     * @return array
     */
    public function refundProfitSharing(array $params): array;

    /**
     * 查询分账回退结果
     * @param array $params
     * @return array
     */
    public function queryRefundProfitSharing(array $params): array;
}