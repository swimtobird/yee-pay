<?php
/**
 *
 * User: swimtobird
 * Date: 2021-01-20
 * Email: <swimtobird@gmail.com>
 */

namespace Swimtobird\YeePay\Gateways\Yee;

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Swimtobird\YeePay\Contracts\GatewayInterface;
use Swimtobird\YeePay\Exceptions\GatewayRequestException;
use Swimtobird\YeePay\Utils\Config;

abstract class AbstractGateway implements GatewayInterface
{
    /**
     * @var Config $config
     */
    protected $config;

    protected $request_id;

    /**
     * @var Client $client
     */
    protected $client;

    const SDK_VERSION = 'yop-auth-v2';

    const EXPIRED_SECONDS = '1800';

    const HOST = 'https://openapi.yeepay.com/yop-center';

    /**
     * AbstractGateway constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->request_id = uniqid('', false);

        $this->client = new Client();
    }

    /**
     * @param array $url
     * @param array $data
     * @return mixed
     */
    public function request(array $urls, array $data)
    {
        $method = $urls['method'];
        $url = $urls['url'];

        $request = new Request($method, $url);

        $headers = $this->getHeaders($request, $data);

        /**
         * @var ResponseInterface $response
         */
        $response = $this->client->$method(self::HOST . $url, [
            'headers' => $headers,
            $this->getRequestMethod($method) => $data
        ]);

        $result = json_decode($response->getBody(),true);

        if ($response->getStatusCode()>=500){
            throw new GatewayRequestException('Yee GatewayError:Server is 500');
        }

        if (isset($result['state']) && 'FAILURE' === $result['state']){
            throw new GatewayRequestException(
                sprintf(
                'Yee Gateway Error: %s, %s',
                $result['error']['code'] ?? '',
                $result['error']['message'] ?? ''
                )
            );
        }
        return $result;
    }

    /**
     * @param string $method
     * @return string
     */
    protected function getRequestMethod(string $method): string
    {
        switch (strtolower($method)) {
            case 'get':
                return 'query';
            case 'post':
                return 'form_params';
        }
    }

    /**
     * @param Request $request
     * @param array $data
     * @return array
     */
    protected function getHeaders(Request $request, array $data): array
    {
        $headers = [
            'x-yop-appkey' => $this->config->get('app_key'),
            'Authorization' => $this->getSign($request, $data)
        ];

        return array_merge($headers, $this->getNecessaryHeaders());
    }

    /**
     * @return array
     */
    protected function getNecessaryHeaders(): array
    {
        $headers = ['x-yop-request-id' => $this->request_id];

        if ($this->config->has('customer_no')) {
            $headers['x-yop-customerid'] = $this->config->get('app_key');
        }

        return $headers;
    }

    /**
     * @param Request $request
     * @param array $data
     * @return string
     */
    protected function getSign(Request $request, array $data): string
    {
        $auth_string = $this->getAuthString();

        openssl_sign(
            $this->formatParameters($request, $auth_string, $data),
            $signature,
            $this->getPrivateKey(),
            OPENSSL_ALGO_SHA256
        );

        $signature = $this->base64UrlEncode($signature);

        return implode('/', [
            "YOP-RSA2048-SHA256 " . $auth_string,
            $this->formatHeaders(),
            $signature,
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getTimestamp()
    {
        ini_set('date.timezone', 'PRC');
        $dataTime = new DateTime();
        return $dataTime->format(DateTime::ISO8601);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getAuthString()
    {
        return implode('/', [
            self::SDK_VERSION,
            $this->config->get('app_key'),
            $this->getTimestamp(),
            self::EXPIRED_SECONDS
        ]);
    }

    /**
     * @param $data
     * @param bool $use_padding
     * @return string
     */
    protected function base64UrlEncode($data, $use_padding = false)
    {
        $encoded = strtr(base64_encode($data), '+/', '-_');

        return (true === $use_padding ? $encoded : rtrim($encoded, '=')) . '$SHA256';
    }

    /**
     * @param Request $request
     * @param string $auth_string
     * @param array $parameters
     * @return string
     */
    protected function formatParameters(Request $request, string $auth_string, array $parameters): string
    {
        ksort($parameters);

        $parameters = urldecode(
            http_build_query(
                array_filter(
                    $parameters,
                    function ($value) {
                        return '' !== $value;
                    }
                )
            )
        );

        return implode("\n", [
            $auth_string,
            $request->getMethod(),
            $request->getUri()->getPath(),
            $parameters,
            $this->getCanonicalHeaders()
        ]);
    }

    /**
     * @return string
     */
    protected function getCanonicalHeaders(): string
    {
        $headers = $this->getNecessaryHeaders();

        $header_string = '';

        if (!empty($headers)) {
            $header_strings = [];

            foreach ($headers as $key => $value) {
                if ($key == null) {
                    continue;
                }
                if ($value == null) {
                    $value = "";
                }
                $key = rawurlencode(strtolower(trim($key)));
                $value = rawurlencode(trim($value));
                array_push($header_strings, $key . ':' . $value);
            }

            sort($header_strings);

            foreach ($header_strings as $kv) {
                $header_string .= strlen($header_string) == 0 ? "" : "\n";
                $header_string .= $kv;
            }
        }
        return $header_string;
    }

    /**
     * @return string
     */
    protected function formatHeaders(): string
    {
        $headers = $this->getNecessaryHeaders();

        $result = '';

        foreach ($headers as $key => $value) {
            $result .= strlen($result) == 0 ? "" : ";";
            $result .= $key;
        }

        $result = strtolower($result);

        return $result;
    }

    /**
     * @return string
     */
    protected function getPrivateKey(): string
    {
        $private_key = '.pem' === substr($this->config->get('app_private_key'), -4)
            ? openssl_pkey_get_private("file://{$this->config->get('app_private_key')}")
            : $this->config->get('app_private_key');

        return "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($private_key, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
    }
}