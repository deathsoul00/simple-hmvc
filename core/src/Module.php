<?php
namespace Core;

use Core\Config;
use Core\Registry;
use Core\Helper\ArrayHelper;
use Core\Exception\LogicException;
use Core\Exception\RuntimeException;
use Core\Exception\InvalidArgumentException;

/**
 * Class that handles Modules controller dispatch and hooks that is defined in config/modules.yml
 *
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */
abstract class Module
{
    /**
     * get modules defined in modules.yml
     *
     * @return array
     */
    public static function getModules()
    {
        return ArrayHelper::msort(Config::get('modules'), 'position');
    }

    /**
     * dispatch module controller
     *
     * @throws Core\Exception\RuntimeException          when namespace is missing in the module config for specific config
     * @throws Core\Exception\InvalidArgumentException  when controller_name is not a valid string
     * @throws Core\Exception\InvalidArgumentException  when method is not a valid string
     * 
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
            foreach ($modules as $module => $module_params) {

                // check if namespace is available in the module
                if (empty($module_params['namespace'])) {
                    throw new RuntimeException(sprintf('module %s does not have a namespace please provide a valid namespace', $module));
                }

                // create the namespace based on module namespace and combined the Controllers\\{Mode}
                $module_namespace = trim($module_params['namespace'], '\\') . '\\Controllers\\' . ucwords($mode);
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
        // dispatch the pre controller module
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
        // dispatch the post controller module
        self::dispatchModuleController($controller_name, $method, 'post', $controller_parameters);
    }

    /**
     * calls out registered hooks for a specific hook name
     *
     * @throws Core\Exception\InvalidArgumentException if hook name is not a valid string
     * @throws Core\Exception\LogicException           if module hook does not contain a valid hook configuration '@' sign
     *
     * @param  string $hook_name unique hook name
     * @param  mixed  &...$args  function arguments
     *
     * @return int
     */
    public static function hook($hook_name, &...$args)
    {
        if (!is_string($hook_name)) {
            throw new InvalidArgumentException(sprintf('hook_name only accepts string as parameter, %s given', gettype($hook_name)));
        }

        if ($modules = self::getModules()) {

            $parameters = $args;

            foreach ($modules as $module => $module_params) {

                if (!empty($module_params['hooks'])) {

                    $module_hooks = $module_params['hooks'];

                    if (isset($module_hooks[$hook_name])) {

                        $hook = $module_hooks[$hook_name];

                        // check if hook of the module is valid and contains a `@` sign seperator for class then method
                        if (strpos($hook, '@') === false) {
                            throw new LogicException(sprintf(
                                'module %s contains an invalid hook config for hook %s. hook config must have `@` sign for the method to be called'
                            ), $module, $hook_name);
                        }

                        list($class_name, $method_name) = explode('@', $hook);
                        // build the class name of the hook defined
                        $class_name = "{$module_params['namespace']}\\Hooks\\$class_name";

                        if (class_exists($class_name)) {

                            $hook_class = new $class_name;
                            $return = call_user_func_array([$hook_class, $method_name], $parameters);

                            // stop propagation of module meaning do not call other hooks anymore
                            if ($return === MODULES_STOP_PROPAGATION) {
                                return;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * override a template being loaded
     *
     * @throws Core\Exception\InvalidArgumentException when template_file is an invalid string
     * @throws Core\Exception\InvalidArgumentException when base_dir is an invalid string or array
     *
     * @param  string       $template_file file name of the template to be overriden
     * @param  array|string $base_dir      base path(s) where templates are stored
     *
     * @return string                filename of the override template else the template file name if no overrides
     */
    public static function templateOverride($template_file, $base_dir, $extension = '')
    {
        if (!$template_file) {
            return $template_file;
        }

        if (!is_string($template_file)) {
            throw new InvalidArgumentException(sprintf('$template_file must be a string, %s given', gettype($template_file)));
        }

        if (is_array($base_dir)) {

            foreach ($base_dir as $dir) {
                $template_file = self::templateOverride($template_file, $dir);
            }

        } elseif (is_string($base_dir)) {

            if ($modules = self::getModules()) {

                foreach ($modules as $module => $configuration) {

                    $override_template = "modules/$module/overrides/$template_file$extension";
                    $override_template_full_path = "$base_dir/$override_template";

                    if (file_exists($override_template_full_path)) {
                        $template_file = $override_template;
                    }
                }

                return $template_file;
            }
        } else {
            throw new InvalidArgumentException(sprintf('$base_dir must be an array or string, %s given', gettype($base_dir)));
        }

        return $template_file;
    }

    public static function hookTemplate($name, $context, $blocks)
    {
        var_dump(func_get_args()); exit;
    }
}
