<?php

namespace JincorTech\AuthClient;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use JincorTech\AuthClient\Exception\AccessTokenNotFound;
use JincorTech\AuthClient\Exception\DecodedSectionNotFound;

/**
 * Class AuthClient
 *
 * @package JincorTech\AuthClient
 */
class AuthClient implements AuthServiceInterface
{
    /**
     * Http Client
     *
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * AuthClient constructor.
     *
     * @param ClientInterface $httpClient
     */
    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $email
     * @param string $password
     * @return TenantRegistrationResult
     * @throws GuzzleException
     * @throws ServerException
     */
    public function registerTenant(string $email, string $password): TenantRegistrationResult
    {
        $response = $this->httpClient->request(
            'POST', '/tenant', [
                'json' => [
                    'email' => $email,
                    'password' => $password,
                ]
            ]
        );

        return new TenantRegistrationResult(json_decode($response->getBody()->getContents(), true));
    }

    /**
     * @param string $email
     * @param string $password
     * @return string
     * @throws AccessTokenNotFound
     * @throws GuzzleException
     * @throws ServerException
     */
    public function loginTenant(string $email, string $password): string
    {
        $response = $this->httpClient->request(
            'POST', '/tenant/login', [
                'json' => [
                    'email' => $email,
                    'password' => $password,
                ]
            ]
        );

        $result = json_decode($response->getBody()->getContents(), true);
        if (array_key_exists('accessToken', $result)) {
            $token = $result['accessToken'];
        } else {
            throw new AccessTokenNotFound();
        }

        return $token;
    }

    /**
     * @param string $tenantToken
     * @return TenantTokenVerificationResult
     * @throws GuzzleException
     * @throws DecodedSectionNotFound
     * @throws ServerException
     */
    public function verifyTenantToken(string $tenantToken): TenantTokenVerificationResult
    {
        $response = $this->httpClient->request(
            'POST', '/tenant/verify', [
                'json' => [
                    'token' => $tenantToken,
                ]
            ]
        );

        $responseData = json_decode($response->getBody()->getContents(), true);
        if (!array_key_exists('decoded', $responseData)) {
            throw new DecodedSectionNotFound('Decoded section not found');
        }

        $params = $responseData['decoded'];
        return new TenantTokenVerificationResult($params);
    }

    /**
     * @param string $tenantToken
     * @void
     * @throws GuzzleException
     * @throws ServerException
     */
    public function logoutTenant(string $tenantToken): void
    {
        $this->httpClient->request(
            'POST', '/tenant/logout', [
                'json' => [
                    'token' => $tenantToken,
                ]
            ]
        );
    }

    /**
     * @param array  $data containing the necessary params
     *      $data = [
     *          'email' => (string) email. Required.
     *          'login' => (string) login. Required.
     *          'password' => (string) password. Required.
     *          'sub' => (string) sub. Required.
     *          'scope' => (array) scope. Optional.
     *      ]
     * @param string $tenantToken
     * @return UserRegistrationResult
     * @throws GuzzleException
     * @throws ServerException
     */
    public function createUser(array $data, string $tenantToken): UserRegistrationResult
    {
        $response = $this->httpClient->request(
            'POST',
            '/user',
            [
                'json' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $tenantToken,
                ]
            ]
        );

        return new UserRegistrationResult(json_decode($response->getBody()->getContents(), true));
    }

    /**
     * @param array  $data containing the necessary params
     *      $data = [
     *          'login' => (string) login. Required.
     *          'password' => (string) password. Required.
     *          'deviceId' => (string) device ID. Required.
     *      ]
     * @param string $tenantToken
     * @return string
     * @throws GuzzleException
     * @throws AccessTokenNotFound
     * @throws ServerException
     */
    public function loginUser(array $data, string $tenantToken): string
    {
        $response = $this->httpClient->request(
            'POST',
            '/auth',
            [
                'json' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $tenantToken,
                ]
            ]
        );

        $result = json_decode($response->getBody()->getContents(), true);

        if (array_key_exists('accessToken', $result)) {
            $token = $result['accessToken'];
        } else {
            throw new AccessTokenNotFound();
        }

        return $token;
    }

    /**
     * @param string $userToken
     * @param string $tenantToken
     * @return UserTokenVerificationResult
     * @throws GuzzleException
     * @throws DecodedSectionNotFound
     * @throws ServerException
     */
    public function verifyUserToken(string $userToken, string $tenantToken): UserTokenVerificationResult
    {
        $response = $this->httpClient->request(
            'POST',
            '/auth/verify',
            [
                'json' => [
                    'token' => $userToken,
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $tenantToken,
                ]
            ]
        );

        $responseData = json_decode($response->getBody()->getContents(), true);
        if (!array_key_exists('decoded', $responseData)) {
            throw new DecodedSectionNotFound('Decoded section not found.');
        }

        $params = $responseData['decoded'];
        return new UserTokenVerificationResult($params);
    }

    /**
     * @param string $userToken
     * @param string $tenantToken
     * @void
     * @throws GuzzleException
     * @throws ServerException
     */
    public function logoutUser(string $userToken, string $tenantToken): void
    {
        $this->httpClient->request(
            'POST',
            '/auth/logout',
            [
                'json' => [
                    'token' => $userToken,
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $tenantToken,
                ]
            ]
        );
    }

    /**
     * @param string $login
     * @param string $tenantToken
     * @void
     * @throws GuzzleException
     * @throws ServerException
     */
    public function deleteUser(string $login, string $tenantToken): void
    {
        $this->httpClient->request(
            'DELETE',
            '/user/' . $login,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $tenantToken,
                ]
            ]
        );
    }
}
