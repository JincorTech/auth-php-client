<?php

namespace JincorTech\AuthClient;

/**
 * Interface AuthServiceInterface.
 */
interface AuthServiceInterface
{
    /**
     * @param string $email
     * @param string $password
     * @return TenantRegistrationResult
     */
    public function registerTenant(string $email, string $password): TenantRegistrationResult;

    /**
     * @param string $email
     * @param string $password
     * @return string
     */
    public function loginTenant(string $email, string $password): string;

    /**
     * @param string $tenantToken
     * @return TenantTokenVerificationResult
     */
    public function verifyTenantToken(string $tenantToken): TenantTokenVerificationResult;

    /**
     * @param string $tenantToken
     * @void
     */
    public function logoutTenant(string $tenantToken);

    /**
     * @param array  $data
     * @param string $tenantToken
     * @return UserRegistrationResult
     */
    public function createUser(array $data, string $tenantToken): UserRegistrationResult;

    /**
     * @param array  $data
     * @param string $tenantToken
     * @return string
     */
    public function loginUser(array $data, string $tenantToken): string;

    /**
     * @param string $userToken
     * @param string $tenantToken
     * @return UserTokenVerificationResult
     */
    public function verifyUserToken(string $userToken, string $tenantToken): UserTokenVerificationResult;

    /**
     * @param string $userToken
     * @param string $tenantToken
     * @void
     */
    public function logoutUser(string $userToken, string $tenantToken);

    /**
     * @param string $login
     * @param string $tenantToken
     * @void
     */
    public function deleteUser(string $login, string $tenantToken);
}
