<?php
/**
 *
 * User: swimtobird
 * Date: 2021-01-31
 * Email: <swimtobird@gmail.com>
 */

namespace Swimtobird\YeePay\Gateways\Wechat;

use Swimtobird\YeePay\Contracts\PayGatewayInterface;

class H5 extends AbstractGateway implements PayGatewayInterface
{
    /**
     * @param array $params
     * @return array
     */
    public function pay(array $params): array
    {
        $params['trade_type'] = $this->getTradeType();

        return $this->preOrder($params)->toArray();
    }

    /**
     * @return string
     */
    protected function getTradeType(): string
    {
        return 'h5';
    }
}