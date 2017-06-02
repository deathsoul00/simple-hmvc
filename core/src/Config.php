<?php
namespace Core;

use Core\Helper\ArrayHelper;
use Symfony\Component\Yaml\Yaml;

/**
 * Config class responsible for loading configs from config directory
 *
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */
class Config
{
    /**
     * singleton instance of the config
     *
     * @var array
     */
    protected static $config = [];

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * load other config if not exists in the instance of $config
     *
     * @param  string $config_file file to be loaded
     *
     * @return void
     */
    public static function loadConfig($config_file)
    {
        $config_file = preg_replace('/(\w+)\.php/i', "$1", $config_file);

        // do not continue process if config is already loaded
        if (self::isConfigLoaded($config_file)) {
            return false;
        }

        if (file_exists($file = getcwd() . "/config/$config_file.yml")) {
            self::$config[$config_file] = Yaml::parse(file_get_contents($file), Yaml::PARSE_CONSTANT);
        }
    }

    /**
     * check if config file is already loaded
     *
     * @param  string  $config_file file to be check if loaded
     *
     * @return boolean             returns true if already loaded otherwise false if not
     */
    public static function isConfigLoaded($config_file)
    {
        return isset(self::$config[$config_file]);
    }

    /**
     * get config variables
     *
     * @param  string $key     dot-noted string key
     * @param  mixed  $default this will be returned if key valued is null or empty
     *
     * @return mixed           value of the key passed else returns the default
     */
    public static function get($key, $default = null)
    {
        // load the other config if not yet exists
        $stack = explode('.', $key);
        $config_file = $stack[0];
        self::loadConfig($config_file);
        // end

        return ArrayHelper::dot(self::$config, $key) ?: $default;
    }
}
