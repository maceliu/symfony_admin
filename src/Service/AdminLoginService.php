<?php


namespace SymfonyAdmin\Service;


use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use SymfonyAdmin\Entity\AdminUser;
use SymfonyAdmin\Exception\CheckFailException;
use SymfonyAdmin\Exception\ExceedLimitException;
use SymfonyAdmin\Exception\NotExistException;
use SymfonyAdmin\Request\AdminUserRequest;
use SymfonyAdmin\Service\Base\BaseService;
use SymfonyAdmin\Service\Base\CurdTrait;
use SymfonyAdmin\Utils\Cache\Keys;
use SymfonyAdmin\Utils\CommonUtils;
use SymfonyAdmin\Utils\Enum\AdminLoginTypeEnum;
use SymfonyAdmin\Utils\Enum\StatusEnum;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use ReflectionException;

class AdminLoginService extends BaseService
{

    use CurdTrait;

    /** @var LoggerInterface */
    private $logger;

    /** @var MailerInterface */
    private $mailer;

    static $checkCodeCountLimit = 5;

    public function __construct(ManagerRegistry $doctrine, LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
        parent::__construct($doctrine);
    }

    /**
     * @param AdminUserRequest $userRequest
     * @param string $loginType
     * @return array
     * @throws CheckFailException
     * @throws NotExistException
     * @throws ReflectionException
     */
    public function login(AdminUserRequest $userRequest, string $loginType = AdminLoginTypeEnum::ADMIN): array
    {
        $em = $this->doctrine->getManager();

        $adminUserRepo = $this->getAdminUserRepo();
        $adminUser = $adminUserRepo->findOneByUsername($userRequest->getUsername());
        if (!$adminUser) {
            throw new NotExistException('用户不存在');
        }

        if ($adminUser->getStatus() == StatusEnum::OFF) {
            throw new NotExistException('用户已被禁用');
        }

        $requestPass = self::makeUserPassword($userRequest->getPassword(), $adminUser->getUsername());
        $this->logger->info('Password | ' . $userRequest->getUsername() . ' ： ' . $requestPass);
        if ($requestPass !== $adminUser->getPassword()) {
            throw new CheckFailException('输入密码错误');
        }

        if ($adminUser->getAdminRole()->getStatus() == StatusEnum::OFF) {
            throw new CheckFailException('用户组已被禁用');
        }

        # 生成token
        $loginToken = $this->generateLoginToken($adminUser);

        # 更新用户资料
        $adminUser->setAccessToken($loginToken);
        $adminUser->setLoginTime(new DateTime());
        $em->persist($adminUser);
        $em->flush();

        if ($loginType == AdminLoginTypeEnum::ADMIN) {
            $r = $adminUser->toArray();
            $r['roles'] = [$adminUser->getAdminRole()->getRoleName()];
            $this->getRedisClient()->set(Keys::adminUserLogin($adminUser->getId()), $loginToken, Keys::USER_LOGIN_EXPIRE_TIME);
        } else {
            $this->getRedisClient()->set(Keys::openApiToken($loginToken), $adminUser->getId(), Keys::OPEN_API_TOKEN_EXPIRE_TIME);
            $r = [
                'accessToken' => $loginToken,
                'expireAt' => date('Y-m-d H:i:s', time() + Keys::OPEN_API_TOKEN_EXPIRE_TIME - 60)
            ];
        }

        return $r;
    }

    /**
     * @param AdminUserRequest $userRequest
     * @return string
     * @throws CheckFailException
     * @throws NotExistException
     * @throws TransportExceptionInterface
     */
    public function findMyPassword(AdminUserRequest $userRequest): string
    {
        $adminUserRepo = $this->getAdminUserRepo();
        $adminUser = $adminUserRepo->findOneByUsername($userRequest->getUsername());
        if (!$adminUser) {
            throw new NotExistException('用户不存在');
        }

        if ($adminUser->getStatus() == StatusEnum::OFF) {
            throw new NotExistException('用户已被禁用');
        }

        if (!CommonUtils::checkIsEmail($adminUser->getEmail())) {
            throw new CheckFailException('用户邮箱格式错误！');
        }

        if (!CommonUtils::checkIsEmail($_ENV['SYSTEM_EMAIL_ACCOUNT'])) {
            throw new CheckFailException('系统邮箱账户配置错误！');
        }

        $checkCode = rand(100000, 999999);
        $this->getRedisClient()->hSet(Keys::passwordCheckCode($adminUser->getId()), Keys::CHECK_CODE, $checkCode);
        $this->getRedisClient()->hSet(Keys::passwordCheckCode($adminUser->getId()), Keys::COUNT, 0);
        $this->getRedisClient()->expire(Keys::passwordCheckCode($adminUser->getId()), Keys::TEN_MIN_CACHE_TIME);

        $email = (new Email())
            ->from($_ENV['SYSTEM_EMAIL_ACCOUNT'])
            ->to($adminUser->getEmail())
            ->subject('后台用户密码找回')
            ->html('<p>尊敬的用户您好，您正在进行密码找回操作，您的验证码是：' . $checkCode . '，此验证码10分钟内有效，如果不是您的找回密码操作，请立即联系系统管理员反馈！</p>');
        $this->mailer->send($email);

        return '邮件已发送至' . $adminUser->getEmail();
    }

    /**
     * @param AdminUserRequest $userRequest
     * @return string
     * @throws NotExistException
     * @throws ExceedLimitException
     * @throws CheckFailException
     */
    public function resetPass(AdminUserRequest $userRequest): string
    {
        $adminUserRepo = $this->getAdminUserRepo();
        $adminUser = $adminUserRepo->findOneByUsername($userRequest->getUsername());
        if (!$adminUser) {
            throw new NotExistException('用户不存在');
        }

        if ($adminUser->getStatus() == StatusEnum::OFF) {
            throw new NotExistException('用户已被禁用');
        }

        $redisCheckCode = $this->getRedisClient()->hGet(Keys::passwordCheckCode($adminUser->getId()), Keys::CHECK_CODE);
        if (empty($redisCheckCode)) {
            throw new NotExistException('未发送验证码，或者验证码已过期，请重新点击发送');
        }

        $count = $this->getRedisClient()->hGet(Keys::passwordCheckCode($adminUser->getId()), Keys::COUNT);
        if ($count >= self::$checkCodeCountLimit) {
            throw new ExceedLimitException('已超出尝试次数，请稍后重新发送验证码！');
        }

        if ($redisCheckCode !== $userRequest->getCheckCode()) {
            $this->getRedisClient()->hSet(Keys::passwordCheckCode($adminUser->getId()), Keys::COUNT, $count + 1);
            throw new CheckFailException('验证码验证失败，请检查后重试！');
        }

        $em = $this->doctrine->getManager();

        $adminUser->setPassword($userRequest->getPassword());

        $em->persist($adminUser);
        $em->flush();

        return 1;
    }


    /**
     * @param string $password
     * @param string $userSalt
     * @return string
     */
    public static function makeUserPassword(string $password, string $userSalt): string
    {
        return md5($password . $userSalt . '_symfony_admin_');
    }

    /**
     * @param AdminUser $adminUser
     * @return string
     */
    private function generateLoginToken(AdminUser $adminUser): string
    {
        return md5(time() . $adminUser->getId() . rand(1, 1000)) . md5('symfony_admin' . rand(100, 999));
    }
}
