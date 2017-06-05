<?php
namespace Core;

use Core\Config;
use Core\Registry;
use Core\Helper\ArrayHelper;
use Core\Exception\LogicException;
use Core\Exception\InvalidArgumentException;

/**
 * Class that handles Modules controller dispatch and hooks that is defined in config/modules.yml
 *
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */
abstract class Module
{
    public static function getModules()
    {
        return ArrayHelper::msort(Config::get('modules'), 'position');
    }

    /**
     * dispatch module controller
     * 
     * @param  string $controller_name       name of the controller to be hook
     * @param  string $method                method called from the main controller
     * @param  string $mode                  pre|post mode of the controller when will occur 
     * @param  array  $controller_parameters parameters passed to the main controller can be pass to the modules contoller
     * 
     * @return void
     */
    protected static function dispatchModuleController($controller_name, $method, $mode = 'pre', array $controller_parameters = [])
    {
        if (!is_string($controller_name)) {
            throw new InvalidArgumentException(sprintf('invalid controller name only accepts string as parameter, %s given', gettype($controller_name)));
        }

        if (!is_string($method)) {
            throw new InvalidArgumentException(sprintf('invalid method name only accepts string as parameter, %s given', gettype($method)));
        }

        // remove the namespace
        $controller_name = substr($controller_name, strrpos($controller_name, '\\') + 1);

        if ($modules = self::getModules()) {
            foreach ($modules as $module) {

                // check if namespace is available in the module
                if (empty($module['namespace'])) {
                    throw new LogicException(sprintf('module %s does not have a namespace please provide a valid namespace'));
                }

                // create the namespace based on module namespace and combined the Controllers\\{Mode}
                $module_namespace = trim($module['namespace'], '\\') . '\\Controllers\\' . ucwords($mode);
                $class_name = "$module_namespace\\$controller_name";

                // check if that class exits the execute the method if also exists
                if (class_exists($class_name)) {
                    $module_controller = new $class_name(Registry::getContainer());
                    if (method_exists($module_controller, $method)) {
                        // call the method and pass the parameters from route
                        call_user_func_array([$module_controller, $method], $controller_parameters);
                    }
                }
            }
        }
    }

    /**
     * pre wrapper for Core\Module::dispatchModuleController
     * 
     * @param  string $controller_name       name of the controller to be hook
     * @param  string $method                method called from the main controller
     * @param  array  $controller_parameters parameters passed to the main controller can be pass to the modules contoller
     * 
     * @return void
     */
    public static function preDispatch($controller_name, $method, array $controller_parameters = [])
    {
        // dispatch the controller module
        self::dispatchModuleController($controller_name, $method, 'pre', $controller_parameters);
    }

    /**
     * post wrapper for Core\Module::dispatchModuleController
     * 
     * @param  string $controller_name       name of the controller to be hook
     * @param  string $method                method called from the main controller
     * @param  array  $controller_parameters parameters passed to the main controller can be pass to the modules contoller
     * 
     * @return void
     */
    public static function postDispatch($controller_name, $method, array $controller_parameters = [])
    {
        // dispatch the controller module
        self::dispatchModuleController($controller_name, $method, 'post', $controller_parameters);
    }
}
