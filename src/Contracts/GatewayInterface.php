<?php
/**
 *
 * User: swimtobird
 * Date: 2021-01-21
 * Email: <swimtobird@gmail.com>
 */

namespace Swimtobird\YeePay\Contracts;


use Swimtobird\YeePay\Utils\Config;

interface GatewayInterface
{
    public function __construct(Config $config);

}