<?php
namespace Core\Factory;

use Core\Config;
use Core\Template\TemplateEngineInterface;

/**
 * creates the Template engine of this framework
 *
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */
class TemplateFactory implements FactoryInterface
{
    /** FactoryInterface::create */
    public static function create()
    {
        // configurations
        $configurations = Config::get('template');
        $engine_name = $configurations['engine'];

        $engine = $configurations[$engine_name];
        $class = new $engine['class'];

        if (!$class instanceOf TemplateEngineInterface) {
            throw new \Core\Exception\RuntimeException(sprintf(
                '%s engine class must be an instance of Core\\Template\\TemplateEngineInterface, %s given',
                get_class($class),
                get_class($class)
            ));
        }

        return $class->initialize($engine['options']);
    }
}
