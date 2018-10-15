<?php
/**
 * @category    jwt-for-pimcore
 * @date        15/10/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */
namespace Divante\UserBundle\Controller;

use Divante\LoginBundle\Service\PasswordService;
use Divante\UserBundle\Command\ChangePassword;
use Divante\UserBundle\Service\UserService;
use Pimcore\Controller\FrontendController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class DefaultController
 * @package Divante\UserBundle\Controller
 */
class DefaultController extends FrontendController
{
    /** @var UserService */
    private $userService;
    /** @var PasswordService  */
    protected $passwordService;

    /**
     * DefaultController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("/api/user/change-password")
     * @param ChangePassword $command
     * @return JsonResponse
     */
    public function changePasswordAction(ChangePassword $command)
    {
        $result = $this->userService->changePassword($command);
        if (!$result) {
            throw new BadRequestHttpException('Invalid current password');
        } else {
            return new JsonResponse();
        }
    }
}
