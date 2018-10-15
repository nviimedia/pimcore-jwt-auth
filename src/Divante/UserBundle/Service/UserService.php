<?php
/**
 * @category    jwt-for-pimcore
 * @date        15/10/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\UserBundle\Service;

use Divante\LoginBundle\Service\AbstractService;
use Divante\LoginBundle\Service\PasswordService;
use Divante\UserBundle\Command\ChangePassword;
use Pimcore\Model\DataObject\User;
use Sabre\DAV\Exception\NotAuthenticated;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserService
 * @package Divante\UserBundle\Service
 */
class UserService extends AbstractService
{

    /** @var PasswordService */
    protected $passwordService;

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /**
     * GetUserDetailsResponse constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param PasswordService       $passwordService
     */
    public function __construct(TokenStorageInterface $tokenStorage, PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
        $this->tokenStorage    = $tokenStorage;
    }

    /**
     * @param ChangePassword $query
     * @return bool
     * @throws NotAuthenticated
     */
    public function changePassword(ChangePassword $query): bool
    {
        if (!$this->passwordService->isPasswordValidForCurrentUser($query->getPassword())) {
            return false;
        }
        /** @var User $user */
        $user = User::getById($this->getCurrentUser()->getId());
        $user->setPassword($query->getNewPassword());
        $user->save();
        return true;
    }

    /**
     * @return UserInterface
     * @throws NotAuthenticated
     */
    public function getCurrentUser(): UserInterface
    {
        $token = $this->tokenStorage->getToken();
        if ($token instanceof TokenInterface) {
            $user = $token->getUser();
            if ($user instanceof UserInterface) {
                return $user;
            }
        }
        throw new NotAuthenticated();
    }
}
