<?php
/**
 *
 * User: swimtobird
 * Date: 2021-01-20
 * Email: <swimtobird@gmail.com>
 */

namespace Swimtobird\YeePay\Exceptions;


use Throwable;

class GatewayException extends \RuntimeException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}