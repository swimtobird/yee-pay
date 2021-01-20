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
use Swimtobird\YeePay\Utils\Config;

/**
 * Class PayProvider
 * @package Swimtobird\YeePay
 *
 * @method  GatewayInterface pay(array $params)
 * @method  GatewayInterface refund(array $params)
 * @method  GatewayInterface query(array $params)
 * @method  GatewayInterface cancel(array $params)
 * @method  GatewayInterface success()
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
            throw new InvalidArgumentException("gateway {$gateway} is not supported");
        }

        return new $class($this->config);
    }

    /**
     * @param $method
     * @param array $arguments
     * @return GatewayInterface
     */
    public function __call($method, array $arguments): GatewayInterface
    {
        return $this->gateway->$method($arguments[0]);
    }
}