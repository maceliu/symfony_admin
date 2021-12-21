<?php

namespace SymfonyAdmin\Utils\RemoteService\Base;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use SymfonyAdmin\Exception\Base\ErrorException;
use Exception;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class BaseRemoteService
{
    /** @var string 接口地址 */
    protected $remoteUrl;

    /** @var string 接口域名 */
    protected $remoteHost;

    /** @var string 接口路径 */
    protected $remotePath;

    /** @var array 请求头 */
    protected $options;

    /** @var string 请求方式 */
    protected $method;

    /** @var array 通用请求头 */
    protected $preQueryParam;

    /** @var Client guzzle请求句柄 */
    private $client;

    /** @var string 响应http状态码 */
    private $httpCode;

    private $httpErrorMsg;

    private $returnMsg;

    public $httpLogger;


    /**
     * @desc 构造函数
     * @param string $remoteHost
     * @param LoggerInterface $httpLogger
     * @param int $timeout
     */
    public function __construct(string $remoteHost, LoggerInterface $httpLogger, int $timeout = 5)
    {
        $this->remoteHost = $remoteHost;
        $this->httpLogger = $httpLogger;

        $this->client = new Client([
            'base_uri' => $remoteHost,
            'timeout' => $timeout
        ]);
    }

    /**
     * 请求前预处理函数
     * @return void
     */
    protected function beforeRequest()
    {
        if (!empty($this->preQueryParam)) {
            $this->options[RequestOptions::QUERY] = array_merge($this->options[RequestOptions::QUERY], $this->preQueryParam);
        }
    }

    /**
     * @desc 调用接口发起请求，并获取指定格式响应内容
     * @param string $method
     * @param string $remotePath
     * @param array $options
     * @return array
     * @throws ErrorException|GuzzleException
     */
    public function getResponseContent(string $method, string $remotePath, array $options): array
    {
        $this->remotePath = $remotePath;
        $this->remoteUrl = $this->remoteHost . $remotePath;
        $this->options = $options;
        $this->method = $method;
        $callStartTime = microtime(true);

        $this->beforeRequest();

        try {
            $response = $this->client->request($this->method, $this->remotePath, $this->options)->getBody()->getContents();
        } catch (Exception $e) {
            $this->httpCode = $e->getCode();
            $this->setHttpErrorMsg($e->getMessage());
            $this->httpLogger->error($this->getHttpErrorMsg());
            throw new ErrorException($e->getMessage());
        }

        $this->httpCode = 200;
        $this->httpLogger->info("请求耗时：" . sprintf('%.4f', microtime(true) - $callStartTime) . $this->requestDateFormat());
        $this->httpLogger->debug("Response : $response");
        return $this->afterResponse($response);
    }

    /**
     * @param $response
     * @return array
     * @throws ErrorException
     */
    protected function afterResponse($response): array
    {
        $responseArr = json_decode($response, true);
        if (!isset($responseArr['status']) || $responseArr['status'] != 200) {
            $msg = $responseArr['message'] ?? '接口调用失败';
            $this->setReturnMsg($msg);
            $this->httpLogger->error($this->getReturnMsg());
            throw new ErrorException($msg);
        }
        return $responseArr['content'];
    }

    public function setHttpErrorMsg($errorMsg)
    {
        $this->httpErrorMsg = 'HTTP REQUEST ERROR (' . $this->getHttpCode() . '): ' . $errorMsg . $this->requestDateFormat();
    }

    public function getHttpErrorMsg(): string
    {
        return $this->httpErrorMsg;
    }

    public function setReturnMsg($returnMsg)
    {
        $this->returnMsg = 'REQUEST RETURN ERROR : ' . $returnMsg . $this->requestDateFormat();
    }

    public function getReturnMsg(): string
    {
        return $this->returnMsg;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    private function requestDateFormat(): string
    {
        $r = ' | url:' . $this->remoteUrl;
        $r .= ' | method:' . $this->method;
        return $r . ' | options:' . json_encode($this->options);
    }

}
