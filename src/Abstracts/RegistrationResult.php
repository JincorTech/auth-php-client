<?php

namespace JincorTech\AuthClient\Abstracts;

use InvalidArgumentException;
use JincorTech\AuthClient\Traits\ValidateParams;

/**
 * Class RegistrationResult.
 */
abstract class RegistrationResult
{
    use ValidateParams;

    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $login;

    /**
     * RegistrationResult constructor.
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->validateParams($params, ['id', 'email', 'login']);

        $this->setId($params['id']);
        $this->setEmail($params['email']);
        $this->setLogin($params['login']);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string $id
     */
    private function setId(string $id)
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Id value can not be empty');
        }

        $this->id = $id;
    }

    /**
     * @param string $email
     */
    private function setEmail(string $email)
    {
        if (empty($email)) {
            throw new InvalidArgumentException('Email value can not be empty');
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid Email address');
        }

        $this->email = $email;
    }

    /**
     * @param string $login
     */
    private function setLogin(string $login)
    {
        if (empty($login)) {
            throw new InvalidArgumentException('Login value can not be empty');
        }

        $this->login = $login;
    }
}
