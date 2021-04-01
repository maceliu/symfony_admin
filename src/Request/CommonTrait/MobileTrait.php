<?php


namespace App\Request\CommonTrait;


use App\Exception\InvalidParamsException;
use App\Utils\CommonUtils;

trait MobileTrait
{
    /** @var string $mobile */
    protected $mobile = '';

    /**
     * @return string
     */
    public function getMobile(): string
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     * @param bool $isCheck
     * @throws InvalidParamsException
     */
    public function setMobile(string $mobile, $isCheck = true): void
    {
        if ($isCheck && !CommonUtils::checkIsMobile($mobile)) {
            throw new InvalidParamsException('传入参数错误！手机号格式错误！' . $mobile);
        }
        $this->mobile = $mobile;
    }
}