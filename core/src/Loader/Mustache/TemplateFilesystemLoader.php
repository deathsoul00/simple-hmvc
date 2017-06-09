<?php
namespace Core\Loader\Mustache;

use Core\Module;

/**
 * A custom Template filesystem loader to load templates applying overrides
 *
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */
class TemplateFilesystemLoader extends \Mustache_Loader_FilesystemLoader
{
    /**
     * template base dir
     * 
     * @var string
     */
    protected $base_dir = '';

    /**
     * template extension
     * 
     * @var string
     */
    protected $extension = '';

    /**
     * class constructor
     *
     * @param string $base_dir base directory path
     * @param array  $options  loader options
     */
    public function __construct($base_dir, array $options = [])
    {
        // call parent constructor
        parent::__construct($base_dir, $options);

        // set base_dir
        $this->base_dir = $base_dir;

        // set extension
        if (array_key_exists('extension', $options)) {
            if (empty($options['extension'])) {
                $this->extension = '';
            } else {
                $this->extension = '.' . ltrim($options['extension'], '.');
            }
        }
    }

    /**
     * override \Mustache_Loader_FilesystemLoader::load
     *
     * @param  string $name name of the file to be loaded
     *
     * @return string       content of the file
     */
    public function load($name)
    {
        // override template file name
        $name = Module::templateOverride($name, $this->base_dir, $this->extension);

        // calls default behavior
        return parent::load($name);
    }
}
