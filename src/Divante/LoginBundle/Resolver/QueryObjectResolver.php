<?php
/**
 * @category    jwt-for-pimcore
 * @date        15/10/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */
namespace Divante\LoginBundle\Resolver;

use Divante\LoginBundle\Query\AbstractQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class QueryObjectResolver
 * @package Divante\LoginBundle\Resolver
 */
class QueryObjectResolver implements ArgumentValueResolverInterface
{
    /** @var ValidatorInterface */
    protected $validator;

    /**
     * QueryObjectResolver constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Whether this resolver can resolve the value for the given ArgumentMetadata.
     *
     * @param Request $request
     * @param ArgumentMetadata $argument
     *
     * @return bool
     * @throws \ReflectionException
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        if (!$argument->getType() || !class_exists($argument->getType())) {
            return false;
        }
        $reflection = new \ReflectionClass($argument->getType());
        return $reflection->isSubclassOf(AbstractQuery::class);
    }

    /**
     * Returns the possible value(s).
     *
     * @param Request $request
     * @param ArgumentMetadata $argument
     *
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $class = $argument->getType();
        $dto = new $class($request);
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $response = [];
            /** @var ConstraintViolationInterface $error */
            foreach ($errors as $error) {
                $response[] = $error->getMessage();
            }
            throw new BadRequestHttpException(implode(" \n ", $response));
        }
        yield $dto;
    }
}