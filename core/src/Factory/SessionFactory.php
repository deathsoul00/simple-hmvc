<?php
namespace Core\Factory;

use Core\Registry;

class SessionFactory implements FactoryInterface
{
    public static function create()
    {
        $session_factory = new \Aura\Session\SessionFactory;
        return $session_factory->newInstance(Registry::get('request')->cookies->all());
    }
}
