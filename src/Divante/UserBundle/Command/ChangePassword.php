<?php
/**
 * @category    jwt-for-pimcore
 * @date        15/10/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\UserBundle\Command;

use Symfony\Component\Validator\Constraints as Assert;
use Divante\LoginBundle\Query\AbstractQuery;

/**
 * Class ChangePassword
 * @package Divante\UserBundle\Command
 */
class ChangePassword extends AbstractQuery
{
    /**
     * @var string
     * @Assert\NotBlank(message="Password param is mandatory")
     */
    protected $password;

    /**
     * @var string
     * @Assert\NotBlank(message="New password param is mandatory")
     */
    protected $newPassword;

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

    /**
     * @return string
     */
    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    /**
     * @param string $newPassword
     */
    public function setNewPassword(string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }
}
