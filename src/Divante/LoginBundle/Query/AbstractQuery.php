<?php
/**
 * @category    jwt-for-pimcore
 * @date        15/10/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */
namespace Divante\LoginBundle\Query;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractQuery
 * @package Divante\LoginBundle\Query
 */
abstract class AbstractQuery
{
    /**
     * AbstractQuery constructor.
     * @param null|Request $request
     */
    public function __construct(Request $request = null)
    {
        if ($request->getContentType() == 'json' && $request->getContent()) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
        }
        if (null !== $request) {
            $params = array_merge($request->attributes->all(), $request->query->all(), $request->request->all());
            $this->setParams($params);
        }
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        foreach ($params as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (is_callable([$this, $setter])) {
                $this->{$setter}($value);
            }
        }
    }
}
