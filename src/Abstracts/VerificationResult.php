<?php

namespace JincorTech\AuthClient\Abstracts;

use InvalidArgumentException;
use JincorTech\AuthClient\Traits\ValidateParams;

/**
 * Class VerificationResult.
 */
abstract class VerificationResult
{
    use ValidateParams;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $jti;

    /**
     * @var int
     */
    private $iat;

    /**
     * @var string
     */
    private $aud;

    /**
     * VerificationResult constructor.
     *
     * @param array $params
     * @throws InvalidArgumentException
     */
    public function __construct(array $params)
    {
        $this->validateParams($params, ['id', 'login', 'jti', 'iat', 'aud']);

        $this->setId($params['id']);
        $this->setLogin($params['login']);
        $this->setJti($params['jti']);
        $this->setIat($params['iat']);
        $this->setAud($params['aud']);
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
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getJti(): string
    {
        return $this->jti;
    }

    /**
     * @return int
     */
    public function getIat(): int
    {
        return $this->iat;
    }

    /**
     * @return string
     */
    public function getAud(): string
    {
        return $this->aud;
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
     * @param string $login
     */
    private function setLogin(string $login)
    {
        if (empty($login)) {
            throw new InvalidArgumentException('Login value can not be empty');
        }

        $this->login = $login;
    }

    /**
     * @param string $jti
     */
    private function setJti(string $jti)
    {
        if (empty($jti)) {
            throw new InvalidArgumentException('Jti value can not be empty');
        }

        $this->jti = $jti;
    }

    /**
     * @param int $iat
     */
    private function setIat(int $iat)
    {
        $this->iat = $iat;
    }

    /**
     * @param string $aud
     */
    private function setAud(string $aud)
    {
        if (empty($aud)) {
            throw new InvalidArgumentException('Aud value can not be empty');
        }

        $this->aud = $aud;
    }
}
