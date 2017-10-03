<?php

use JincorTech\AuthClient\TenantRegistrationResult;
use JincorTech\AuthClient\UserRegistrationResult;

class RegistrationResultCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function testCreateTenantRegistrationResult(UnitTester $I)
    {
        $params = [
            'id' => '123',
            'email' => 'test@test.com',
            'login' => 'test_login'
        ];

        $userRegistrationResult = new TenantRegistrationResult($params);

        $I->assertEquals('123', $userRegistrationResult->getId());
        $I->assertEquals('test@test.com', $userRegistrationResult->getEmail());
        $I->assertEquals('test_login', $userRegistrationResult->getLogin());
    }

    public function testCreateTenantRegistrationResultWithWrongData(UnitTester $I)
    {
        $params = [
            'id' => '123',
            'email' => 'test@test.com',
            'login' => 'test_login'
        ];

        $checkList = [
            [new InvalidArgumentException('Id value can not be empty'), ['id' => '']],
            [new InvalidArgumentException('Required key: id is not specified'), ['id' => null]],
            [new InvalidArgumentException('Invalid Email address'), ['email' => 'wrong@example_com']],
            [new InvalidArgumentException('Email value can not be empty'), ['email' => '']],
            [new InvalidArgumentException('Required key: email is not specified'), ['email' => null]],
            [new InvalidArgumentException('Login value can not be empty'), ['login' => '']],
            [new InvalidArgumentException('Required key: login is not specified'), ['login' => null]],
        ];

        foreach ($checkList as $item) {
            $I->testExceptionWithWrongData(
                TenantRegistrationResult::class,
                $item[0],
                $params,
                $item[1]
            );
        }
    }

    public function testCreateUserRegistrationResult(UnitTester $I)
    {
        $scope = json_encode([
            'admin' => [
                'create' => true,
                'read' => true,
                'update' => true,
                'delete' => false
            ]
        ]);

        $params = [
            'id' => '123',
            'email' => 'test@test.com',
            'login' => 'test_login',
            'tenant' => 'tenant-id',
            'sub' => 'sub-id',
            'scope' => $scope
        ];

        $userRegistrationResult = new UserRegistrationResult($params);

        $I->assertEquals('123', $userRegistrationResult->getId());
        $I->assertEquals('test@test.com', $userRegistrationResult->getEmail());
        $I->assertEquals('test_login', $userRegistrationResult->getLogin());
        $I->assertEquals('tenant-id', $userRegistrationResult->getTenant());
        $I->assertEquals('sub-id', $userRegistrationResult->getSub());
        $I->assertEquals($scope, $userRegistrationResult->getScope());
    }

    public function testCreateUserRegistrationResultWithoutScope(UnitTester $I)
    {
        $params = [
            'id' => '123',
            'email' => 'test@test.com',
            'login' => 'test_login',
            'tenant' => 'tenant-id',
            'sub' => 'sub-id',
        ];

        $userRegistrationResult = new UserRegistrationResult($params);

        $I->assertEquals('123', $userRegistrationResult->getId());
        $I->assertEquals('test@test.com', $userRegistrationResult->getEmail());
        $I->assertEquals('test_login', $userRegistrationResult->getLogin());
        $I->assertEquals('tenant-id', $userRegistrationResult->getTenant());
        $I->assertEquals('sub-id', $userRegistrationResult->getSub());
        $I->assertEquals('', $userRegistrationResult->getScope());
    }

    public function testCreateUserRegistrationResultWithWrongData(UnitTester $I)
    {
        $scope = json_encode([
            'admin' => [
                'create' => true,
                'read' => true,
                'update' => true,
                'delete' => false
            ]
        ]);

        $params = [
            'id' => '123',
            'email' => 'test@test.com',
            'login' => 'test_login',
            'tenant' => 'tenant-id',
            'sub' => 'sub-id',
            'scope' => $scope
        ];

        $checkList = [
            [new InvalidArgumentException('Id value can not be empty'), ['id' => '']],
            [new InvalidArgumentException('Required key: id is not specified'), ['id' => null]],
            [new InvalidArgumentException('Invalid Email address'), ['email' => 'wrong@example_com']],
            [new InvalidArgumentException('Email value can not be empty'), ['email' => '']],
            [new InvalidArgumentException('Required key: email is not specified'), ['email' => null]],
            [new InvalidArgumentException('Login value can not be empty'), ['login' => '']],
            [new InvalidArgumentException('Required key: login is not specified'), ['login' => null]],
            [new InvalidArgumentException('Tenant value can not be empty'), ['tenant' => '']],
            [new InvalidArgumentException('Required key: tenant is not specified'), ['tenant' => null]],
            [new InvalidArgumentException('Sub value can not be empty'), ['sub' => '']],
            [new InvalidArgumentException('Required key: sub is not specified'), ['sub' => null]],
        ];

        foreach ($checkList as $item) {
            $I->testExceptionWithWrongData(
                UserRegistrationResult::class,
                $item[0],
                $params,
                $item[1]
            );
        }
    }
}
