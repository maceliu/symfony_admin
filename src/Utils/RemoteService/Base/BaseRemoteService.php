<?php

namespace SymfonyAdmin\Utils\RemoteService\Base;

use GuzzleHttp\Exception\GuzzleException;
use SymfonyAdmin\Exception\Base\ErrorException;
use Exception;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class BaseRemoteService
{
    private $client;

    private $httpErrorMsg;

    private $returnMsg;

    private $httpCode;

    private $url;

    private $baseUri;

    private $options;

    private $method;

    public $httpLogger;

    /**
     * @desc 构造函数
     * @param string $baseUri
     * @param LoggerInterface $httpLogger
     * @param int $timeout
     */
    public function __construct(string $baseUri, LoggerInterface $httpLogger, $timeout = 5)
    {
        $this->baseUri = $baseUri;
        $this->httpLogger = $httpLogger;

        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout' => $timeout
        ]);
    }

    /**
     * @desc 调用接口发起请求，并获取指定格式响应内容
     * @param string $method
     * @param string $url
     * @param array $options
     * @return array
     * @throws ErrorException|GuzzleException
     */
    public function getResponseContent(string $method, string $url, array $options): array
    {
        $this->url = $this->baseUri . $url;
        $this->options = $options;
        $this->method = $method;
        $callStartTime = microtime(true);

        try {
            $response = $this->client->request($method, $url, $options)->getBody()->getContents();
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
        $r = '';
        $r .= ' | url:' . $this->url;
        $r .= ' | method:' . $this->method;
        return $r . ' | options:' . json_encode($this->options);
    }

}
