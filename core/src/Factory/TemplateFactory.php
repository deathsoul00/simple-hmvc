<?php
namespace Core\Factory;

use Core\Config;
use Core\Template;

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
        $configuration = Config::get('template');
        $options = @$configuration['options'] ?: [];
        $base_dir = Config::get('paths.resources_dir');

        // attach main template loader
        $configuration['loader'] = new \Mustache_Loader_FilesystemLoader("$base_dir/$configuration[templates_path]/", $options);
        // attach partial template loader
        $configuration['partials_loader'] = new \Mustache_Loader_FilesystemLoader("$base_dir/$configuration[partial_templates_path]/", $options);

        // removed unnecessary configuration
        unset($configuration['templates_path']);
        unset($configuration['partial_templates_path']);
        unset($configuration['options']);

        // return instance of \Mustache_Engine
        return new Template($configuration);
    }
}
