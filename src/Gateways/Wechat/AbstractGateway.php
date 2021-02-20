<?php
/**
 *
 * User: swimtobird
 * Date: 2021-01-30
 * Email: <swimtobird@gmail.com>
 */

namespace Swimtobird\YeePay\Gateways\Wechat;


use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Swimtobird\YeePay\Contracts\GatewayInterface;
use Swimtobird\YeePay\Exceptions\GatewayRequestException;
use Swimtobird\YeePay\Utils\Config;
use WechatPay\GuzzleMiddleware\Util\PemUtil;
use WechatPay\GuzzleMiddleware\WechatPayMiddleware;

abstract class AbstractGateway implements GatewayInterface
{
    const PAY_URL = [
        'method' => 'post',
        'url'    => 'https://api.mch.weixin.qq.com/v3/pay/transactions/'
    ];

    const QUERY_URL = [
        'method' => 'get',
        'url'    => 'https://api.mch.weixin.qq.com/v3/pay/transactions/out-trade-no/%s'
    ];

    const CANCEL_URL = [
        'method' => 'post',
        'url'    => 'https://api.mch.weixin.qq.com/v3/pay/transactions/out-trade-no/%s/close'
    ];

    const REFUND_URL = [
        'method' => 'post',
        'url'    => 'https://api.mch.weixin.qq.com/v3/refund/domestic/refunds'
    ];

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $signParameters;

    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->createClient();
    }

    /**
     * @param array $urls
     * @param array $params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(array $urls, array $params = [])
    {
        $data = [
            'headers' => ['Accept' => 'application/json']
        ];

        if ($params) {
            $data = array_merge($data, [
                'json' => $params
            ]);
        }
        /**
         * @var ResponseInterface $response
         */
        $response = $this->client->request($urls['method'], $urls['url'], $data);

        $result = json_decode($response->getBody(), true);

        if (isset($result['code'])) {
            throw new GatewayRequestException(
                sprintf(
                    'Wechat Gateway Error: %s, %s',
                    $result['code'] ?? '',
                    $result['message'] ?? ''
                )
            );
        }
        return $result;
    }

    public function createClient(): void
    {
        $wechatPayMiddleware = WechatPayMiddleware::builder()
            ->withMerchant(
                $this->config->get('mch_id'),
                $this->config->get('mch_number'),
                $this->getPrivateKey()
            )
            ->withWechatPay([$this->getCertificate()])
            ->build();

        $stack = HandlerStack::create();
        $stack->push($wechatPayMiddleware, 'wechatpay');

        $this->client = new Client(['handler' => $stack]);
    }

    /**
     * @return bool|resource
     */
    protected function getPrivateKey()
    {
        return PemUtil::loadPrivateKey($this->config->get('app_private_key'));
    }

    /**
     * @return bool|resource
     */
    protected function getCertificate()
    {
        return PemUtil::loadCertificate($this->config->get('app_certificate'));
    }

    /**
     * @param array $params
     * @return \Illuminate\Support\Collection
     */
    protected function preOrder(array $params): Collection
    {
        $urls = self::PAY_URL;
        $urls['url'] = $urls['url'] . $this->getTradeType();

        $payload = array_merge($this->getPayload(), $params);

        return collect($this->request($urls, $payload));
    }

    /**
     * @return array
     */
    protected function getPayload()
    {
        return [
            'appid' => $this->config->get('app_id'),
            'mchid' => $this->config->get('mch_id'),
        ];
    }

    /**
     * @return string
     */
    abstract protected function getTradeType(): string;

    /**
     * @return array
     */
    protected function getSignParameters()
    {
        return $this->signParameters;
    }

    /**
     * @return string
     */
    protected function getSign(): string
    {
        $content = implode("\n", $this->getSignParameters());

        openssl_sign(
            $content,
            $signature,
            $this->getPrivateKey(),
            OPENSSL_ALGO_SHA256
        );

        return base64_encode($signature);
    }

    /**
     * @param array $params
     * @return array
     */
    public function query(array $params): array
    {
        $urls = self::QUERY_URL;

        $urls['url'] = sprintf($urls['url'], $params['out_trade_no'] ?? '');

        return $this->request($urls, [
            'mchid'        => $this->config->get('mch_id'),
            'out_trade_no' => $params['out_trade_no']
        ]);
    }

    /**
     * @param array $params
     * @return array
     */
    public function cancel(array $params): array
    {
        $urls = self::CANCEL_URL;

        $urls['url'] = sprintf($urls['url'], $params['out_trade_no'] ?? '');

        return $this->request($urls, [
            'mchid'        => $this->config->get('mch_id'),
            'out_trade_no' => $params['out_trade_no']
        ]);
    }

    /**
     * @param array $params
     * @return array
     */
    public function refund(array $params): array
    {
        return $this->request(self::REFUND_URL, $params);
    }

    /**
     * @return string
     */
    public function success(): string
    {
        return (new Response())->getReasonPhrase();
    }
}