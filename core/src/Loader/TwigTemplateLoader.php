<?php
namespace Core\Loader;

use Core\Module;

class TwigTemplateLoader extends \Twig_Loader_Filesystem implements TemplateLoaderInterface
{
    /**
     * get the template contents for rendering
     * 
     * @param  string $name  filename of the file
     * 
     * @return string        content of the file
     */
    public function findTemplate($name)
    {
        $name = Module::templateOverride($name, $this->paths);

        return parent::findTemplate($name);
    }

    /**
     * returns the base paths of the template
     *
     * @param string $namespace key of the paths to be returned
     * 
     * @return array
     */
    public function getPaths($namespace = self::MAIN_NAMESPACE)
    {
        return parent::getPaths($namespace);
    }
}