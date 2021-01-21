<?php
/**
 *
 * User: swimtobird
 * Date: 2021-01-19
 * Email: <swimtobird@gmail.com>
 */

namespace Swimtobird\YeePay;


use InvalidArgumentException;
use Swimtobird\YeePay\Contracts\PayGatewayInterface;
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
 */
class PayProvider
{
    protected $config;

    /**
     * @var PayGatewayInterface
     */
    protected $gateway;

    public function __construct(string $gateway, array $config)
    {
        $this->config = new Config($config);

        $this->gateway = $this->createGateway($gateway);
    }

    /**
     * @param $gateway
     * @return PayGatewayInterface
     */
    public function createGateway($gateway): PayGatewayInterface
    {
        list($platform, $gateway) = explode('_', $gateway, 2);

        $class = __NAMESPACE__ . '\\Gateways\\' . ucfirst($platform) . '\\' . $gateway;


        if (!class_exists($class)) {
            throw new InvalidArgumentException("gateway {$gateway} is not supported");
        }

        return new $class($this->config);
    }

    /**
     * @param $method
     * @param array $arguments
     * @return PayGatewayInterface
     */
    public function __call($method, array $arguments): PayGatewayInterface
    {
        return $this->gateway->$method($arguments[0]);
    }
}