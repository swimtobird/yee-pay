<?php
/**
 *
 * User: swimtobird
 * Date: 2021-01-19
 * Email: <swimtobird@gmail.com>
 */

namespace Swimtobird\YeePay;


use InvalidArgumentException;
use Swimtobird\YeePay\Contracts\GatewayInterface;
use Swimtobird\YeePay\Contracts\PayGatewayInterface;
use Swimtobird\YeePay\Contracts\ProfitSharingGatewayInterface;
use Swimtobird\YeePay\Utils\Config;

/**
 * Class PayProvider
 * @package Swimtobird\YeePay
 *
 * @method  PayGatewayInterface pay(array $params)
 * @method  PayGatewayInterface refund(array $params)
 * @method  PayGatewayInterface query(array $params)
 * @method  PayGatewayInterface cancel(array $params)
 * @method  PayGatewayInterface success()
 *
 * @method  ProfitSharingGatewayInterface profitSharing(array $params)
 * @method  ProfitSharingGatewayInterface queryProfitSharing(array $params)
 * @method  ProfitSharingGatewayInterface finishProfitSharing(array $params)
 * @method  ProfitSharingGatewayInterface addReceiver(array $params)
 * @method  ProfitSharingGatewayInterface removeReceiver(array $params)
 * @method  ProfitSharingGatewayInterface refundProfitSharing(array $params)
 * @method  ProfitSharingGatewayInterface queryRefundProfitSharing(array $params)
 */
class PayProvider
{
    protected $config;

    /**
     * @var GatewayInterface
     */
    protected $gateway;

    public function __construct(string $gateway, array $config)
    {
        $this->config = new Config($config);

        $this->gateway = $this->createGateway($gateway);
    }

    /**
     * @param $gateway
     * @return GatewayInterface
     */
    public function createGateway($gateway): GatewayInterface
    {
        list($platform, $gateway) = explode('_', $gateway, 2);

        $class = __NAMESPACE__ . '\\Gateways\\' . ucfirst($platform) . '\\' . $gateway;


        if (!class_exists($class)) {
            throw new InvalidArgumentException("Sorry,Gateway {$gateway} is not supported now.");
        }

        return new $class($this->config);
    }

    /**
     * @param $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, array $arguments)
    {
        if (!method_exists($this->gateway,$method)){
            throw new InvalidArgumentException("Sorry,it is not supported {$method} method now.");
        }

        return $this->gateway->$method($arguments[0]);
    }
}