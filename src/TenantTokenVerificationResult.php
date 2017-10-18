<?php

namespace JincorTech\AuthClient;

use JincorTech\AuthClient\Abstracts\VerificationResult;

/**
 * Class TenantTokenVerificationResult.
 */
class TenantTokenVerificationResult extends VerificationResult
{
    /**
     * @var bool
     */
    private $isTenant;

    /**
     * TenantTokenVerificationResult constructor.
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->validateParams($params, ['isTenant']);

        $this->setIsTenant($params['isTenant']);
    }

    /**
     * @return bool
     */
    public function isTenant(): bool
    {
        return $this->isTenant;
    }

    /**
     * @param bool $isTenant
     */
    private function setIsTenant(bool $isTenant)
    {
        $this->isTenant = $isTenant;
    }
}
