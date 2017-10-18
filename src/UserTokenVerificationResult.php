<?php

namespace JincorTech\AuthClient;

use InvalidArgumentException;
use JincorTech\AuthClient\Abstracts\VerificationResult;

/**
 * Class UserTokenVerificationResult.
 */
class UserTokenVerificationResult extends VerificationResult
{
    /**
     * @var string
     */
    private $scope;

    /**
     * @var string
     */
    private $deviceId;

    /**
     * @var string
     */
    private $sub;

    /**
     * @var int
     */
    private $exp;

    /**
     * UserTokenVerificationResult constructor.
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->validateParams($params, ['deviceId', 'sub', 'exp']);

        $this->setDeviceId($params['deviceId']);
        $this->setSub($params['sub']);
        $this->setExp($params['exp']);
        $this->setScope($params['scope'] ?? '');
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @return string
     */
    public function getDeviceId(): string
    {
        return $this->deviceId;
    }

    /**
     * @return string
     */
    public function getSub(): string
    {
        return $this->sub;
    }

    /**
     * @return int
     */
    public function getExp(): int
    {
        return $this->exp;
    }

    /**
     * @param string $scope
     */
    private function setScope(string $scope)
    {
        $this->scope = $scope;
    }

    /**
     * @param string $deviceId
     */
    private function setDeviceId(string $deviceId)
    {
        if (empty($deviceId)) {
            throw new InvalidArgumentException('DeviceId value can not be empty');
        }

        $this->deviceId = $deviceId;
    }

    /**
     * @param string $sub
     */
    private function setSub(string $sub)
    {
        if (empty($sub)) {
            throw new InvalidArgumentException('Sub value can not be empty');
        }

        $this->sub = $sub;
    }

    /**
     * @param int $exp
     */
    private function setExp(int $exp)
    {
        $this->exp = $exp;
    }
}
