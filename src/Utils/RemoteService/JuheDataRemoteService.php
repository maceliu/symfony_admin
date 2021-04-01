<?php


namespace App\Utils\RemoteService;


use App\Exception\Base\ErrorException;
use App\Exception\InvalidParamsException;
use App\Utils\RemoteService\Base\BaseRemoteService;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Exception;

class JuheDataRemoteService extends BaseRemoteService
{
    /** @var string */
    private $appKey;

    public function __construct(LoggerInterface $httpLogger, $timeout = 30)
    {
        $this->appKey = $_ENV['JUHE_APP_KEY'];
        parent::__construct($_ENV['JUHE_HOST'], $httpLogger, $timeout);
    }

    /**
     * @param string $date
     * @return array
     * @throws ErrorException
     * @throws InvalidParamsException
     */
    public function getJuheCalendarData(string $date): array
    {
        if (empty($date)) {
            throw new InvalidParamsException('传入参数错误');
        }
        $options = [
            RequestOptions::FORM_PARAMS => [
                'date' => $date,
                'key' => $this->appKey
            ]
        ];
        $responseArr = $this->getResponseContent('POST', '/calendar/day', $options);
        if (is_null($responseArr['result'])) {
            throw new Exception('接口调用失败,错误信息：' . $responseArr['error_code'] ?? '未知错误');
        }

        return $responseArr['result']['data'];
    }

    /**
     * @param $response
     * @return array
     * @throws Exception
     */
    protected function afterResponse($response): array
    {
        $responseArr = json_decode($response, true);
        if ($responseArr['reason'] != 'Success' && (empty($responseArr['result']))) {
            throw new Exception('聚合数据万年历调用失败，错误信息：' . $responseArr['error_code'] ?? '未知错误');
        }

        return $responseArr;
    }
}