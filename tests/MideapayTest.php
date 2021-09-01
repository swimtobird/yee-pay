<?php
/**
 *
 * User: swimtobird
 * Date: 2021-08-28
 * Email: <swimtobird@gmail.com>
 */

namespace Swimtobird\YeePay\Tests;


use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Swimtobird\YeePay\PayProvider;

class MideapayTest extends TestCase
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var PayProvider
     */
    private $payProvider;

    protected function setUp(): void
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();

        $this->config = [
            'partner' => $_ENV['MIDEAPAY_PARTNER'],
            'sign_url' => $_ENV['MIDEAPAY_SIGN_URL']
        ];

        $this->payProvider = new PayProvider('Mideapay_H5', $this->config);
    }

    public function testPay()
    {
        $result = $this->payProvider->pay([
            'notify_url' => $_ENV['MIDEAPAY_PAY_NOTIFY_URL'],
            'return_url' => $_ENV['MIDEAPAY_PAY_RETURN_URL'],
            'is_guarantee' => 'FALSE',
            'out_trade_no' => time(),
            'out_trade_time' => date('YmdHis'),
            'payer_type' => 'C',
            'payer_login_name' => '',
            'currency_type' => 'CNY',
            'order_amount' => '10000',
            'pay_amount' => '10000',
            'is_virtual_product' => 'TRUE',
            'product_name' => '网约车充值',
            'product_info' => '网约车充值',
        ]);

        $this->assertArrayHasKey('pay_url',$result);
    }

    public function testQuery()
    {
        $result = $this->payProvider->query([
            'out_trade_no' => 1630464551,
        ]);

//        var_dump($result);

        $this->assertArrayHasKey('result_code',$result);

    }

    public function testRefund()
    {
        $result = $this->payProvider->refund([
            'out_trade_no'    => 1630464551,
            'out_refund_time' => date('YmdHis'),
            'out_refund_no'   => 1630464551,
            'refund_amount'   => 10000,
            'notify_url'      => $_ENV['MIDEAPAY_REFUND_NOTIFY_URL'],
        ]);

//        var_dump($result);

        $this->assertArrayHasKey('result_code',$result);
    }
}