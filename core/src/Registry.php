<?php
namespace Core;

use Core\Route;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * A singleton class responsible for connecting to the container to get the services
 *
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */
class Registry
{
    /**
     * @var Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected static $container;

    /**
     * Initialized container
     * 
     * @return void
     */
    public static function boot()
    {
        self::$container = new ContainerBuilder;
        $loader = new YamlFileLoader(self::$container, new FileLocator(Config::get('paths.root_dir')));
        $loader->load('services.yml');
    }

    /**
     * returns the current container
     * 
     * @return Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public static function getContainer()
    {
        if (self::$container == null) {
            self::boot();
        }
        return self::$container;
    }

    /**
     * get specific service in the container
     * 
     * @param  string $service name of the servie to be called
     * 
     * @return mixed
     */
    public static function get($service)
    {
        return self::getContainer()->get($service);
    }
}
