<?php
/**
 *
 * User: swimtobird
 * Date: 2021-08-28
 * Email: <swimtobird@gmail.com>
 */

namespace Swimtobird\YeePay\Gateways\Mideapay;


use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Swimtobird\YeePay\Contracts\GatewayInterface;
use Swimtobird\YeePay\Exceptions\GatewayRequestException;
use Swimtobird\YeePay\Utils\Config;

abstract class AbstractGateway implements GatewayInterface
{
    /**
     * @var Config $config
     */
    protected $config;

    /**
     * @var Client $client
     */
    protected $client;

    /**
     * 自建签名WEB地址
     * @var string
     */
    protected $sign_url;

    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->request_id = uniqid('', false);

        $this->client = new Client();

        $this->sign_url = $this->config->get('sign_url');
    }

    /**
     * @return string
     */
    protected function getHost()
    {
        if ($this->config->get('is_dev',true)){
            return 'https://in.mideaepayuat.com/gateway.htm';
        }else{
            return 'https://in.mideaepay.com/gateway.htm';
        }
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function request(array $data)
    {
        $sign = $this->getSign($data);

        $data = array_merge($data,[
            'sign' => $sign
        ]);

        $response = $this->client->POST($this->getHost(), [
            'form_params' => $data
        ]);

        if ($response->getStatusCode()>=500){
            throw new GatewayRequestException('Midea GatewayError:Server is 500');
        }

        return json_decode($response->getBody(),true);
    }

    /**
     * @param array $data
     * @return string
     */
    protected function getSign(array $data): string
    {
        ksort($data);

        $sign = md5(
            urldecode(
                http_build_query(
                    array_filter(
                        $data,
                        function ($value) {
                            return '' !== $value;
                        }
                    )
                )
            )
        );

        $response = $this->client->get($this->sign_url.'/rsa/sign.htm'."?source={$sign}");

        $result = json_decode($response->getBody(),true);

        if ($response->getStatusCode()>=500){
            throw new GatewayRequestException('Midea GatewayError:Server is 500');
        }

        if (!isset($result['code']) && $result['code'] !== '1'){
            throw new GatewayRequestException('Midea GatewayError:Server is 500');
        }

        return Arr::get($result,'sign');
    }
}