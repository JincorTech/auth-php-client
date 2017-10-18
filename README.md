Auth client
![](https://travis-ci.org/JincorTech/auth-php-client.svg?branch=master)
===========

This is a client library which encapsulates interaction with [Jincor Auth](https://github.com/JincorTech/backend-auth). With its help you can:
1. Register users and tenants.
2. Get tokens for users and tenants after authorization.
3. Perform verification of tokens for users and tenants.
4. Deactivate tokens for users and tenants.
5. Remove users.

The user can be attached to several tenants through the field `tenant`. It is filled from the tenant's token in the Jincor Auth service.

Usage
-----
### Initialize Auth client
To interact with the HTTP protocol use [Guzzle](https://github.com/guzzle/guzzle). Headers `Accept: application/json` and `Content-Type: application/json` are mandatory.

```php
$authClient = new AuthClient(new Client([
    'base_uri' => 'auth:3000',
    'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ]
]));
```

### Work with Tenant
```php
$tenant = $authClient->registerTenant('tenant@example.com', 'Passwpord');
echo $tenant->getId();
// 'af8b13ea-02a9-4e73-b8d9-58c8215757b9'
$tenantToken = $authClient->loginTenant('tenant@example.com', 'Passwpord');
$result = $authClient->verifyTenantToken($tenantToken);
echo $result->getAud();
// 'jincor.com'
$authClient->logoutTenant($tenantToken);
```

### Work with User
To work with users you need a tenant token. Field `scope` is optional.
```php
$userData = [
    'email' => 'user@example.com',
    'password' => 'Password1',
    'login' => 'emp_dev',
    'sub' => '123',
    'scope' => [
        'admin',
        'settings' => 'setting',
    ]
];
$user = $authClient->createUser($userData, $tenantToken);
echo $user->getId();
// '55096b7d-0f14-446a-b50d-ee6bc8431e39'

$userData = [
    'login' => 'emp_dev',
    'password' => 'Password1',
    'deviceId' => '123',
];
$userToken = $authClient->loginUser($userData, $tenantToken);
$result = $authClient->verifyUserToken($userToken, $tenantToken);
$authClient->logoutUser($userToken, $tenantToken);

$authClient->deleteUser($userData['login'], $tenantToken);
```

More details can be received in the tests.

### Project setup
1. Clone the repo
2. `cd /path/to/repo`
3. `docker-compose build` - build development containers
4. `docker-compose up -d` - run container

#### Local testing
To run all tests just type `docker-compose exec workspace ./vendor/bin/codecept run`
