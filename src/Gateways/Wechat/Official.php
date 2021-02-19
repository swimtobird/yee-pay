<?php
/**
 *
 * User: swimtobird
 * Date: 2021-01-30
 * Email: <swimtobird@gmail.com>
 */

namespace Swimtobird\YeePay\Gateways\Wechat;


use Illuminate\Support\Str;
use Swimtobird\YeePay\Contracts\PayGatewayInterface;

class Official extends AbstractGateway implements PayGatewayInterface
{
    /**
     * @param array $params
     * @return array
     */
    public function pay(array $params): array
    {
        $params['trade_type'] = $this->getTradeType();

        $prepay_id = $this->preOrder($params)->get('prepay_id');

        $data = [
            'appid'     => $this->config->get('app_id'),
            'partnerid' => $this->config->get('mch_id'),
            'prepayid'  => $prepay_id,
            'timestamp' => strval(time()),
            'noncestr'  => Str::random(),
            'package'   => 'prepay_id='.$prepay_id,
            'signType' => 'RSA'
        ];

        $this->setSignParameters($data);

        $data['sign'] = $this->getSign();

        return $data;
    }

    /**
     * @return string
     */
    protected function getTradeType(): string
    {
        return 'jsapi';
    }

    /**
     * @param $data
     */
    protected function setSignParameters($data)
    {
        $this->signParameters = [
            'appid'     => $data['app_id'],
            'timestamp' => $data['timestamp'],
            'noncestr'  => $data['noncestr'],
            'package'   => $data['package'],
        ];
    }
}