<?php
namespace Core;

use Core\Config;
use Core\Registry;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Main application class handles the boot and the dispatching of events/controllers
 *
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */
class Application
{
    /**
     * initialized necessary registries/services
     *
     * @return void
     */
    public static function boot()
    {
        // register services
        Registry::boot();
    }

    /**
     * dispatches the resource
     *
     * @return void
     */
    public static function dispatch()
    {
        $request = Registry::get('request');
        $dispatch = $request->get('dispatch', '/');
        $router = Registry::get('router');

        try {
            $route_details = $router->match($dispatch);

            $controller = $route_details['_controller'];
            unset($route_details['_controller']);

            $method = !isset($route_details['_method']) ? 'index' : $route_details['_method'];
            unset($route_details['_method']);

            $route_name = $route_details['_route'];
            unset($route_details['_route']);

            if (!class_exists($controller)) {
                throw new \Core\Exception\ClassNotFoundException(sprintf('%s controller not found', $controller));
            }

            // initialize controller
            $controller = new $controller(Registry::getContainer());

            // reserved for pre-controller

            // call method of the class
            $response = call_user_func_array([$controller, $method], $route_details);

            // reserved for post controller

            $vars = Registry::get('template')->getAssignedVars();
            $content = Registry::get('template')->render($controller->getTemplate(), $vars);

            if ($controller->getLayout()) {
                $vars['content'] = $content;
                echo Registry::get('template')->render($controller->getLayout(), $vars);
            } else {
                echo $content;
            }

        } catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $ex) {
            throw new \Core\Exception\ResourceNotFoundException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
        }
    }
}