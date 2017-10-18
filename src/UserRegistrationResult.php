<?php

namespace JincorTech\AuthClient;

use InvalidArgumentException;
use JincorTech\AuthClient\Abstracts\RegistrationResult;

/**
 * Class UserRegistrationResult.
 */
class UserRegistrationResult extends RegistrationResult
{
    /**
     * @var string
     */
    private $tenant;

    /**
     * @var string
     */
    private $scope;

    /**
     * @var string
     */
    private $sub;

    /**
     * UserRegistrationResult constructor.
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->validateParams($params, ['tenant', 'sub']);

        $this->setTenant($params['tenant']);
        $this->setSub($params['sub']);
        $this->setScope($params['scope'] ?? '');
    }

    /**
     * @return string
     */
    public function getTenant(): string
    {
        return $this->tenant;
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
    public function getSub(): string
    {
        return $this->sub;
    }

    /**
     * @param string $tenant
     */
    private function setTenant(string $tenant)
    {
        if (empty($tenant)) {
            throw new InvalidArgumentException('Tenant value can not be empty');
        }

        $this->tenant = $tenant;
    }

    /**
     * @param string $scope
     */
    private function setScope(string $scope)
    {
        $this->scope = $scope;
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
}
