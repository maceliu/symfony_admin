<?php


namespace SymfonyAdmin\Request\CommonTrait;


use SymfonyAdmin\Exception\InvalidParamsException;

trait CityTrait
{
    /** @var int */
    protected $districtId;

    /** @var int */
    protected $provinceId;

    /** @var int */
    protected $cityId;

    /** @var int */
    protected $districtCode;

    /**
     * @return int
     */
    public function getProvinceId(): int
    {
        return $this->provinceId;
    }

    /**
     * @param int $provinceId
     * @throws InvalidParamsException
     */
    public function setProvinceId(int $provinceId): void
    {
        if (empty($provinceId) || $provinceId > 9999) {
            throw new InvalidParamsException('地区ID格式错误');
        }
        $this->provinceId = $provinceId;
    }

    /**
     * @return int
     */
    public function getCityId(): int
    {
        return $this->cityId;
    }

    /**
     * @param int $cityId
     * @throws InvalidParamsException
     */
    public function setCityId(int $cityId): void
    {
        if (empty($cityId) || $cityId > 9999) {
            throw new InvalidParamsException('地区ID格式错误');
        }
        $this->cityId = $cityId;
    }

    /**
     * @return int
     */
    public function getDistrictId(): int
    {
        return $this->districtId;
    }

    /**
     * @param int $districtId
     * @throws InvalidParamsException
     */
    public function setDistrictId(int $districtId): void
    {
        if (empty($districtId) || $districtId > 9999) {
            throw new InvalidParamsException('地区ID格式错误');
        }
        $this->districtId = $districtId;
    }

    /**
     * @return int
     */
    public function getDistrictCode(): int
    {
        return $this->districtCode;
    }

    /**
     * @param int $districtCode
     * @throws InvalidParamsException
     */
    public function setDistrictCode(int $districtCode): void
    {
        if (empty($districtCode) || $districtCode > 999999 || $districtCode < 100000) {
            throw new InvalidParamsException('地区编码错误');
        }
        $this->districtCode = $districtCode;
    }


}