<?php
namespace Core\Factory;

/**
 * FactoryInterface is the interface implemented by all the factory class in Core classes
 *
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */
interface FactoryInterface
{
    /**
     * method being called when registered in the services.yml
     * 
     * @return mixed
     */
    public static function create();
}
