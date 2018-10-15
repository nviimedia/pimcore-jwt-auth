<?php
/**
 * @category    jwt-for-pimcore
 * @date        15/10/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\LoginBundle\Service;

use Pimcore\Security\Encoder\Factory\UserAwareEncoderFactory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class PasswordService
 * @package Divante\LoginBundle\Service
 */
class PasswordService extends AbstractService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var UserAwareEncoderFactory  */
    protected $encoderFactory;

    /**
     * GetUserDetailsResponse constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return UserAwareEncoderFactory
     */
    protected function getEncoderFactory(): UserAwareEncoderFactory
    {
        if (!$this->encoderFactory instanceof UserAwareEncoderFactory) {
            $this->encoderFactory = $this->container->get('security.encoder_factory');
        }
        return $this->encoderFactory;
    }
    /**
     * @param $password
     * @return bool
     * @throws \Sabre\DAV\Exception\NotAuthenticated
     */
    public function isPasswordValidForCurrentUser($password): bool
    {
        $user = $this->getCurrentUser();
        $encoder = $this->getEncoderFactory()->getEncoder($user);
        return $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());
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