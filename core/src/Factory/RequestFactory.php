<?php
namespace Core\Factory;

use Symfony\Component\HttpFoundation\Request;

/**
 * creates the Symfony\Component\HttpFoundation\Request class which will contain
 * the request variables of php
 *
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */
class RequestFactory implements FactoryInterface
{
    /** FactoryInterface::create */
    public static function create()
    {
        return Request::createFromGlobals();
    }
}