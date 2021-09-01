<?php
/**
 *
 * User: swimtobird
 * Date: 2021-01-31
 * Email: <swimtobird@gmail.com>
 */

namespace Swimtobird\YeePay\Gateways\Alipay;


use Alipay\EasySDK\Kernel\Factory;
use Alipay\EasySDK\Kernel\Util\ResponseChecker;
use GuzzleHttp\Client;
use Swimtobird\YeePay\Contracts\GatewayInterface;
use Swimtobird\YeePay\Utils\Config;

abstract class AbstractGateway implements GatewayInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Client
     */
    protected $client;

    public function __construct(Config $config)
    {
        $this->config = $config;

        Factory::setOptions($this->setConfig($config));
    }

    protected function setConfig(Config $config)
    {
        $options = new \Alipay\EasySDK\Kernel\Config();
        $options->protocol = 'https';
        $options->gatewayHost = 'openapi.alipay.com';
        $options->signType = 'RSA2';

        $options->appId = '<-- 请填写您的AppId，例如：2019022663440152 -->';

        // 为避免私钥随源码泄露，推荐从文件中读取私钥字符串而不是写入源码中
        $options->merchantPrivateKey = '<-- 请填写您的应用私钥，例如：MIIEvQIBADANB ... ... -->';

        $options->alipayCertPath = '<-- 请填写您的支付宝公钥证书文件路径，例如：/foo/alipayCertPublicKey_RSA2.crt -->';
        $options->alipayRootCertPath = '<-- 请填写您的支付宝根证书文件路径，例如：/foo/alipayRootCert.crt" -->';
        $options->merchantCertPath = '<-- 请填写您的应用公钥证书文件路径，例如：/foo/appCertPublicKey_2019051064521003.crt -->';

        //注：如果采用非证书模式，则无需赋值上面的三个证书路径，改为赋值如下的支付宝公钥字符串即可
        // $options->alipayPublicKey = '<-- 请填写您的支付宝公钥，例如：MIIBIjANBg... -->';

        //可设置异步通知接收服务地址（可选）
        $options->notifyUrl = "<-- 请填写您的支付类接口异步通知接收服务地址，例如：https://www.test.com/callback -->";

        //可设置AES密钥，调用AES加解密相关接口时需要（可选）
        $options->encryptKey = "<-- 请填写您的AES密钥，例如：aa4BtZ4tspm2wnXLb1ThQA== -->";

        return $options;
    }

    public function request($url, array $params)
    {
        try {
            //2. 发起API调用（以支付能力下的统一收单交易创建接口为例）
            $result = Factory::payment()
                ->common()
                ->create("iPhone6 16G", "20200326235526001", "88.88", "2088002656718920");
            $responseChecker = new ResponseChecker();
            //3. 处理响应或异常
            if ($responseChecker->success($result)) {
                echo "调用成功" . PHP_EOL;
            } else {
                echo "调用失败，原因：" . $result->msg . "，" . $result->subMsg . PHP_EOL;
            }
        } catch (\Exception $e) {
            echo "调用失败，" . $e->getMessage() . PHP_EOL;
        }
    }
}