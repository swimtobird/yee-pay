<?php
/**
 *
 * User: swimtobird
 * Date: 2021-01-30
 * Email: <swimtobird@gmail.com>
 */

namespace Swimtobird\YeePay\Exceptions;


use Throwable;

class InvalidConfigException extends \InvalidArgumentException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}