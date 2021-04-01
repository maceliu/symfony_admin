<?php


namespace App\Utils;


class CommonUtils
{
    /**
     * @param string|int $mobile
     * @return bool
     */
    public static function checkIsMobile($mobile): bool
    {
        if (!empty($mobile) && is_numeric($mobile) && strlen($mobile) == 11 && intval($mobile[0]) == 1) {
            return true;
        }
        return false;
    }

    /**
     * @param string $email
     * @return bool
     */
    public static function checkIsEmail(string $email): bool
    {
        $result = trim($email);
        if (filter_var($result, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $centNum
     * @return string
     */
    public static function cent2Yuan(int $centNum): string
    {
        return round(($centNum / 100), 2);
    }

    /**
     * @param string $mobile
     * @return string|string[]
     */
    public static function mobileHidden(string $mobile)
    {
        return substr_replace($mobile, '****', 3, 4);
    }
}