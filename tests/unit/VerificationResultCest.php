<?php

use JincorTech\AuthClient\UserTokenVerificationResult;
use JincorTech\AuthClient\TenantTokenVerificationResult;

class VerificationResultCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function testCreateTenantTokenVerificationResult(UnitTester $I)
    {
        $params = [
            'id' => 'id-123',
            'login' => 'login-test',
            'jti' => 'jti-uuid',
            'iat' => 123456789,
            'aud' => 'jincor.com',
            'isTenant' => true,
        ];

        $tenantTokenVerificationResult = new TenantTokenVerificationResult($params);

        $I->assertEquals('id-123', $tenantTokenVerificationResult->getId());
        $I->assertEquals('login-test', $tenantTokenVerificationResult->getLogin());
        $I->assertEquals('jti-uuid', $tenantTokenVerificationResult->getJti());
        $I->assertEquals(123456789, $tenantTokenVerificationResult->getIat());
        $I->assertEquals('jincor.com', $tenantTokenVerificationResult->getAud());
        $I->assertEquals(true, $tenantTokenVerificationResult->isTenant());
    }

    public function testCreateTenantTokenVerificationResultWithWrongData(UnitTester $I)
    {
        $params = [
            'id' => 'id-123',
            'login' => 'login-test',
            'jti' => 'jti-uuid',
            'iat' => 123456789,
            'aud' => 'jincor.com',
            'isTenant' => true,
        ];

        $checkList = [
            [new InvalidArgumentException('Id value can not be empty'), ['id' => '']],
            [new InvalidArgumentException('Required key: id is not specified'), ['id' => null]],
            [new InvalidArgumentException('Login value can not be empty'), ['login' => '']],
            [new InvalidArgumentException('Required key: login is not specified'), ['login' => null]],
            [new InvalidArgumentException('Jti value can not be empty'), ['jti' => '']],
            [new InvalidArgumentException('Required key: jti is not specified'), ['jti' => null]],
            [new InvalidArgumentException('Aud value can not be empty'), ['aud' => '']],
            [new InvalidArgumentException('Required key: aud is not specified'), ['aud' => null]],
            [new InvalidArgumentException('Required key: isTenant is not specified'), ['isTenant' => null]],
            [new InvalidArgumentException('Required key: iat is not specified'), ['iat' => null]],
        ];

        foreach ($checkList as $item) {
            $I->testExceptionWithWrongData(
                TenantTokenVerificationResult::class,
                $item[0],
                $params,
                $item[1]
            );
        }
    }

    public function testCreateUserTokenVerificationResult(UnitTester $I)
    {
        $scope = json_encode([
            'admin' => [
                'create' => true,
                'read' => true,
                'update' => true,
                'delete' => false,
            ],
        ]);

        $params = [
            'id' => 'id-123',
            'login' => 'login-test',
            'jti' => 'jti-uuid',
            'iat' => '123456789',
            'aud' => 'jincor.com',
            'deviceId' => 'dev-123',
            'sub' => 'sub-123',
            'exp' => 12345678,
            'scope' => $scope,
        ];

        $userTokenVerificationResult = new UserTokenVerificationResult($params);

        $I->assertEquals('id-123', $userTokenVerificationResult->getId());
        $I->assertEquals('login-test', $userTokenVerificationResult->getLogin());
        $I->assertEquals('jti-uuid', $userTokenVerificationResult->getJti());
        $I->assertEquals(123456789, $userTokenVerificationResult->getIat());
        $I->assertEquals('jincor.com', $userTokenVerificationResult->getAud());
        $I->assertEquals('dev-123', $userTokenVerificationResult->getDeviceId());
        $I->assertEquals('sub-123', $userTokenVerificationResult->getSub());
        $I->assertEquals(12345678, $userTokenVerificationResult->getExp());
        $I->assertEquals($scope, $userTokenVerificationResult->getScope());
    }

    public function testCreateUserTokenVerificationResultWithoutScope(UnitTester $I)
    {
        $params = [
            'id' => 'id-123',
            'login' => 'login-test',
            'jti' => 'jti-uuid',
            'iat' => '123456789',
            'aud' => 'jincor.com',
            'deviceId' => 'dev-123',
            'sub' => 'sub-123',
            'exp' => 12345678,
        ];

        $userTokenVerificationResult = new UserTokenVerificationResult($params);

        $I->assertEquals('id-123', $userTokenVerificationResult->getId());
        $I->assertEquals('login-test', $userTokenVerificationResult->getLogin());
        $I->assertEquals('jti-uuid', $userTokenVerificationResult->getJti());
        $I->assertEquals(123456789, $userTokenVerificationResult->getIat());
        $I->assertEquals('jincor.com', $userTokenVerificationResult->getAud());
        $I->assertEquals('dev-123', $userTokenVerificationResult->getDeviceId());
        $I->assertEquals('sub-123', $userTokenVerificationResult->getSub());
        $I->assertEquals(12345678, $userTokenVerificationResult->getExp());
        $I->assertEquals('', $userTokenVerificationResult->getScope());
    }
}
