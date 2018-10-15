<?php
/**
 * @category    jwt-for-pimcore
 * @date        15/10/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */
namespace Divante\LoginBundle\Query;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class LoginQuery
 * @package Divante\LoginBundle\Query
 */
class LoginQuery extends AbstractQuery
{
    /**
     * @var string
     * @Assert\NotBlank(message="Login must not be empty")
     */
    protected $username;

    /**
     * @var string
     * @Assert\NotBlank(message="Password must not be empty")
     */
    protected $password;

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

}