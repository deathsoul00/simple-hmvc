<?php
namespace Core\Factory;

use Core\Config;
use Symfony\Component\Routing\Router;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Loader\YamlFileLoader;

/**
 * creates the Symfony\Component\Routing\Router class which is responsible for the routing
 * of this framework
 *
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */
class RouterFactory implements FactoryInterface
{
    /** FactoryInterface::create */
    public static function create()
    {
        // create a file locator instance 
        $file_locator = new FileLocator(Config::get('paths.root_dir'));
        $request_context = new RequestContext('/');
        $cache_dir = ['cache_dir' => Config::get('paths.cache_dir') . '/routes'];

        // return Router Class
        return new Router(new YamlFileLoader($file_locator), 'routes.yml' , $cache_dir, $request_context);
    }
}
