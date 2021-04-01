<?php


namespace SymfonyAdmin\Entity\Base;


use SymfonyAdmin\Request\Base\BaseRequest;
use SymfonyAdmin\Utils\CommonUtils;
use SymfonyAdmin\Utils\Enum\TimeFormatEnum;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\PersistentCollection;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Doctrine\ORM\Mapping as ORM;

class BaseEntity
{
    # 数组化展示时不显示的字段
    protected $hiddenProperties = [];

    # 自动更新逻辑不可更新的字段
    protected $noUpdateProperties = [];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="create_time", type="datetime", nullable=false, options={"comment"="创建时间"})
     */
    protected $createTime;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="update_time", type="datetime", nullable=false, options={"comment"="最后修改时间"})
     */
    protected $updateTime;

    public function __construct()
    {
        $this->setCreateTime(new DateTime());
        $this->setUpdateTime(new DateTime());
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getCreateTime(): DateTime
    {
        return $this->createTime;
    }

    /**
     * @param DateTimeInterface $createTime
     * @return $this
     */
    public function setCreateTime(DateTimeInterface $createTime): self
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdateTime(): DateTime
    {
        return $this->updateTime;
    }

    /**
     * @param DateTimeInterface $updateTime
     * @return $this
     */
    public function setUpdateTime(DateTimeInterface $updateTime): self
    {
        $this->updateTime = $updateTime;

        return $this;
    }


    /**
     * @param bool $isHiddenProperties
     * @return array
     * @throws ReflectionException
     */
    public function toArray(bool $isHiddenProperties = true): array
    {
        $class = new ReflectionClass($this);
        $arr = [];

        foreach ($class->getProperties(ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE) as $property) {
            $name = $property->getName();
            if ($isHiddenProperties && in_array($name, $this->hiddenProperties ?? [])) {
                continue;
            }
            if ('_' == $name[0]) {
                $name = substr($name, 1);
            }
            $functionName = $name;
            $functionName[0] = strtoupper($functionName[0]);
            $getFunctionName = 'get' . $functionName;

            if (method_exists($this, $getFunctionName)) {
                $value = $this->{$getFunctionName}();
                if ($value instanceof BaseEntity) {
                    $value = $value->getId();
                } elseif (is_array($value) || $value instanceof PersistentCollection) {
                    foreach ($value as $key => $item) {
                        if ($item instanceof BaseEntity) {
                            $arr[$name][$key] = $item->toArray();
                        } else {
                            $arr[$name][$key] = $item;
                        }
                    }
                    continue;
                }

                # 价格类字段格式化处理为单位元，保留两位小数
                if (substr($name, -5) == 'Price' && is_int($value)) {
                    $arr[$name] = CommonUtils::cent2Yuan($value);
                } elseif (substr($name, -4) == 'Time' && $value instanceof DateTimeInterface) {
                    $arr[$name] = $value->format(TimeFormatEnum::DEFAULT_TIME_SEC);
                } else {
                    $arr[$name] = $value;
                }

            }
        }

        return $arr;
    }

    /**
     * @param array $data
     * @param BaseRequest $request
     */
    public function updateFromRequest(array $data, BaseRequest $request)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, ['id', 'createTime', 'updateTime']) || in_array($key, $this->noUpdateProperties)) {
                continue;
            }

            $setterMethod = 'set' . $key;
            $getterMethod = 'get' . $key;
            if (!method_exists($request, $setterMethod) || !method_exists($this, $setterMethod)) {
                continue;
            }

            $request->$setterMethod($value);
            $this->$setterMethod($request->$getterMethod());
        }
    }
}
