<?php

use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use JincorTech\AuthClient\AuthClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use JincorTech\AuthClient\UserRegistrationResult;
use JincorTech\AuthClient\TenantRegistrationResult;
use JincorTech\AuthClient\UserTokenVerificationResult;
use JincorTech\AuthClient\Exception\AccessTokenNotFound;
use JincorTech\AuthClient\TenantTokenVerificationResult;
use JincorTech\AuthClient\Exception\DecodedSectionNotFound;

class AuthClientCest
{
    /**
     * @var Client
     */
    private $mockHttpClient;

    /**
     * @var MockHandler
     */
    private $mockHandler;

    /**
     * @var AuthClient
     */
    private $authClient;

    /**
     * @var array
     */
    private $container;

    public function _before(UnitTester $I)
    {
        $this->container = [];
        $history = Middleware::history($this->container);

        $this->mockHandler = new MockHandler();
        $handler = HandlerStack::create($this->mockHandler);
        $handler->push($history);

        $this->mockHttpClient = new Client(['handler' => $handler]);
        $this->authClient = new AuthClient($this->mockHttpClient);
    }

    public function _after(UnitTester $I)
    {
    }

    public function testRegisterTenant(UnitTester $I)
    {
        $responseBody = json_encode([
            'id' => 'uuid',
            'login' => 'tenant:test@test22.com',
            'email' => 'test@test22.com',
        ]);
        $requestBody = json_encode([
            'email' => 'test@test22.com',
            'password' => 'Password1',
        ]);

        $this->addResponseToHandler($responseBody);

        $registrationResult = $this->authClient->registerTenant('test@test22.com', 'Password1');

        $I->assertInstanceOf(TenantRegistrationResult::class, $registrationResult);
        $I->assertEquals('uuid', $registrationResult->getId());
        $I->assertEquals('tenant:test@test22.com', $registrationResult->getLogin());
        $I->assertEquals('test@test22.com', $registrationResult->getEmail());

        $request = $this->getLastRequest();
        $this->assertRequest($request, 'POST', '/tenant', $requestBody, $I);
    }

    public function testRegisterTenantWithWrongData(UnitTester $I)
    {
        $responseBody = json_encode([
            'errors' => '...',
        ]);

        $this->addFailedResponseToHandler($responseBody);

        $I->expectException(GuzzleException::class, function () {
            $this->authClient->registerTenant('test@test22.com', 'Password1');
        });
    }

    public function testLoginTenant(UnitTester $I)
    {
        $responseBody = json_encode([
            'accessToken' => 'jwt_token',
        ]);
        $requestBody = json_encode([
            'email' => 'test@test22.com',
            'password' => 'Password1',
        ]);

        $this->addResponseToHandler($responseBody);

        $I->assertEquals('jwt_token', $this->authClient->loginTenant('test@test22.com', 'Password1'));

        $request = $this->getLastRequest();
        $this->assertRequest($request, 'POST', '/tenant/login', $requestBody, $I);
    }

    public function testLoginTenantWithEmptyResponse(UnitTester $I)
    {
        $responseBody = json_encode([]);
        $requestBody = json_encode([
            'email' => 'test@test22.com',
            'password' => 'Password1',
        ]);

        $this->addResponseToHandler($responseBody);

        $I->expectException(AccessTokenNotFound::class, function () {
            $this->authClient->loginTenant('test@test22.com', 'Password1');
        });

        $request = $this->getLastRequest();
        $this->assertRequest($request, 'POST', '/tenant/login', $requestBody, $I);
    }

    public function testVerifyTenantToken(UnitTester $I)
    {
        $responseBody = json_encode([
            'decoded' => [
                'id' => 'uuid',
                'login' => 'tenant:test@test22.com',
                'jti' => '69c29215-1951-47f5-8b23-e5b8deb109441506351136272',
                'iat' => 1506351136272,
                'aud' => 'jincor.com',
                'isTenant' => true,
            ],
        ]);
        $requestBody = json_encode([
            'token' => 'jwt_token',
        ]);

        $this->addResponseToHandler($responseBody);

        $tenantTokenVerificationResult = $this->authClient->verifyTenantToken('jwt_token');
        $I->assertInstanceOf(TenantTokenVerificationResult::class, $tenantTokenVerificationResult);
        $I->assertEquals('uuid', $tenantTokenVerificationResult->getId());
        $I->assertEquals('tenant:test@test22.com', $tenantTokenVerificationResult->getLogin());
        $I->assertEquals('69c29215-1951-47f5-8b23-e5b8deb109441506351136272', $tenantTokenVerificationResult->getJti());
        $I->assertEquals(1506351136272, $tenantTokenVerificationResult->getIat());
        $I->assertEquals('jincor.com', $tenantTokenVerificationResult->getAud());
        $I->assertEquals(true, $tenantTokenVerificationResult->isTenant());

        $request = $this->getLastRequest();
        $this->assertRequest($request, 'POST', '/tenant/verify', $requestBody, $I);
    }

    public function testVerifyTenantTokenWithEmptyResponse(UnitTester $I)
    {
        $responseBody = json_encode([]);
        $requestBody = json_encode([
            'token' => 'jwt_token',
        ]);

        $this->addResponseToHandler($responseBody);

        $I->expectException(DecodedSectionNotFound::class, function () use ($I) {
            $I->assertTrue($this->authClient->verifyTenantToken('jwt_token'));
        });

        $request = $this->getLastRequest();
        $this->assertRequest($request, 'POST', '/tenant/verify', $requestBody, $I);
    }

    public function testLogoutTenant(UnitTester $I)
    {
        $responseBody = json_encode([
            'result' => 1,
        ]);
        $requestBody = json_encode([
            'token' => 'jwt_token',
        ]);

        $this->addResponseToHandler($responseBody);
        $this->authClient->logoutTenant('jwt_token');
        $request = $this->getLastRequest();
        $this->assertRequest($request, 'POST', '/tenant/logout', $requestBody, $I);
    }

    public function testCreateUser(UnitTester $I)
    {
        $scope = json_encode([
            'admin',
            'writer',
            'root',
        ]);

        $userData = [
            'email' => 'empl_1@test22.com',
            'password' => 'Password1',
            'login' => 'empl_1',
            'sub' => '123',
            'scope' => $scope,
        ];
        $responseBody = json_encode([
            'id' => 'uuid',
            'email' => 'empl_1@test22.com',
            'login' => 'empl_1',
            'sub' => '123',
            'tenant' => 'uuid',
            'scope' => $scope,
        ]);

        $requestBody = json_encode($userData);

        $this->addResponseToHandler($responseBody);

        $userRegistrationResult = $this->authClient->createUser($userData, 'jwt_token');
        $I->assertInstanceOf(UserRegistrationResult::class, $userRegistrationResult);
        $I->assertEquals('uuid', $userRegistrationResult->getId());
        $I->assertEquals('empl_1', $userRegistrationResult->getLogin());
        $I->assertEquals('empl_1@test22.com', $userRegistrationResult->getEmail());
        $I->assertEquals('uuid', $userRegistrationResult->getTenant());
        $I->assertEquals('123', $userRegistrationResult->getSub());
        $I->assertEquals($scope, $userRegistrationResult->getScope());

        $request = $this->getLastRequest();
        $this->assertRequest($request, 'POST', '/user', $requestBody, $I);
    }

    public function testLoginUser(UnitTester $I)
    {
        $userData = [
            'password' => 'Password1',
            'login' => 'empl_1',
            'deviceId' => '123',
        ];
        $responseBody = '{
                "accessToken": "token_123"
            }';
        $requestBody = json_encode($userData);

        $this->addResponseToHandler($responseBody);

        $I->assertEquals('token_123', $this->authClient->loginUser($userData, 'jwt_token'));

        /**
         * @var Request
         */
        $request = $this->getLastRequest();
        $this->assertRequest($request, 'POST', '/auth', $requestBody, $I);
    }

    public function testLoginUserWith400Response(UnitTester $I)
    {
        $userData = [
            'password' => 'Password1',
            'login' => 'empl_1',
            'deviceId' => '123',
        ];
        $responseBody = '{
                "error": "some error"
            }';

        $this->addFailedResponseToHandler($responseBody);

        $I->expectException(GuzzleException::class, function () use ($userData) {
            $this->authClient->loginUser($userData, 'jwt_token');
        });
    }

    public function testLoginUserWith500Response(UnitTester $I)
    {
        $userData = [
            'password' => 'Password1',
            'login' => 'empl_1',
            'deviceId' => '123',
        ];
        $responseBody = '{
                "error": "some error"
            }';

        $this->add500ResponseToHandler($responseBody);

        $I->expectException(ServerException::class, function () use ($userData) {
            $this->authClient->loginUser($userData, 'jwt_token');
        });
    }

    public function testLoginUserWithEmptyResponse(UnitTester $I)
    {
        $userData = [
            'password' => 'Password1',
            'login' => 'empl_1',
            'deviceId' => '123',
        ];
        $responseBody = json_encode([]);
        $requestBody = json_encode($userData);

        $this->addResponseToHandler($responseBody);

        $I->expectException(AccessTokenNotFound::class, function () use ($I, $userData) {
            $I->assertEquals('token_123', $this->authClient->loginUser($userData, 'jwt_token'));
        });

        $request = $this->getLastRequest();
        $this->assertRequest($request, 'POST', '/auth', $requestBody, $I);
    }

    public function testVerifyUserToken(UnitTester $I)
    {
        $scope = json_encode([
            'admin',
            'writer',
            'root',
        ]);

        $responseBody = json_encode([
            'decoded' => [
                'id' => 'uuid',
                'login' => 'empl_1',
                'deviceId' => '123',
                'jti' => '...',
                'iat' => 1506286948251,
                'sub' => '123',
                'aud' => 'jincor.com',
                'exp' => 1506287553051,
                'scope' => $scope,
            ],
        ]);
        $requestBody = json_encode([
            'token' => 'token_123',
        ]);

        $this->addResponseToHandler($responseBody);

        $userTokenVerificationResult = $this->authClient->verifyUserToken('token_123', 'jwt_token');

        $I->assertInstanceOf(UserTokenVerificationResult::class, $userTokenVerificationResult);
        $I->assertEquals('uuid', $userTokenVerificationResult->getId());
        $I->assertEquals('empl_1', $userTokenVerificationResult->getLogin());
        $I->assertEquals('123', $userTokenVerificationResult->getDeviceId());
        $I->assertEquals('...', $userTokenVerificationResult->getJti());
        $I->assertEquals(1506286948251, $userTokenVerificationResult->getIat());
        $I->assertEquals('123', $userTokenVerificationResult->getSub());
        $I->assertEquals('jincor.com', $userTokenVerificationResult->getAud());
        $I->assertEquals(1506287553051, $userTokenVerificationResult->getExp());
        $I->assertEquals($scope, $userTokenVerificationResult->getScope());

        $request = $this->getLastRequest();
        $this->assertRequest($request, 'POST', '/auth/verify', $requestBody, $I);
    }

    public function testVerifyUserTokenWithEmptyResponse(UnitTester $I)
    {
        $responseBody = json_encode([]);
        $requestBody = json_encode([
            'token' => 'token_123',
        ]);

        $this->addResponseToHandler($responseBody);

        $I->expectException(DecodedSectionNotFound::class, function () use ($I) {
            $I->assertTrue($this->authClient->verifyUserToken('token_123', 'jwt_token'));
        });

        $request = $this->getLastRequest();
        $this->assertRequest($request, 'POST', '/auth/verify', $requestBody, $I);
    }

    public function testLogoutUser(UnitTester $I)
    {
        $responseBody = '
            {
                "result": 1
            }';
        $requestBody = json_encode([
            'token' => 'token_123',
        ]);

        $this->addResponseToHandler($responseBody);

        $this->authClient->logoutUser('token_123', 'jwt_token');

        $request = $this->getLastRequest();
        $this->assertRequest($request, 'POST', '/auth/logout', $requestBody, $I);
    }

    public function testDeleteUser(UnitTester $I)
    {
        $responseBody = '
            {
                "result": 1
            }';
        $requestBody = '';

        $this->addResponseToHandler($responseBody);

        $this->authClient->deleteUser('empl_1', 'jwt_token');

        $request = $this->getLastRequest();

        $I->assertEquals('DELETE', $request->getMethod());
        $I->assertEquals('/user/empl_1', $request->getRequestTarget());
        $I->assertEquals($requestBody, $request->getBody()->getContents());
    }

    private function assertRequest(
        Request $request,
        string $method,
        string $requestTarget,
        string $requestBody,
        UnitTester $I
    ) {
        $I->assertEquals($method, $request->getMethod());
        $I->assertEquals($requestTarget, $request->getRequestTarget());
        $I->assertJsonStringEqualsJsonString($requestBody, $request->getBody()->getContents());
    }

    private function getLastRequest(): Request
    {
        return array_pop($this->container)['request'];
    }

    /**
     * @param $responseBody
     */
    private function addResponseToHandler($responseBody)
    {
        $this->mockHandler->append(
            new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8',
            ], $responseBody)
        );
    }

    private function addFailedResponseToHandler($responseBody)
    {
        $this->mockHandler->append(
            new Response(400, [
                'Content-Type' => 'application/json; charset=utf-8',
            ], $responseBody)
        );
    }

    private function add500ResponseToHandler($responseBody)
    {
        $this->mockHandler->append(
            new Response(500, [
                'Content-Type' => 'application/json; charset=utf-8',
            ], $responseBody)
        );
    }
}
