<?php
/**
 * @category    jwt-for-pimcore
 * @date        15/10/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\LoginBundle\Model\DataObject;

use Pimcore\Model\DataObject\ClassDefinition\Data\Password;
use Pimcore\Model\DataObject\User as BaseUser;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package Divante\LoginBundle\Model\DataObject
 */
class User extends BaseUser implements UserInterface
{
    /**
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        /** @var Password $field */
        $field = $this->getClass()->getFieldDefinition('password');
        $field->getDataForResource($this->getPassword(), $this);
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles ?? [];
    }
}