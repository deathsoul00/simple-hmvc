<?php
namespace Core\Loader\Twig;

use Core\Module;

class TemplateFilesystemLoader extends \Twig_Loader_Filesystem
{
    public function findTemplate($name)
    {
        $name = Module::templateOverride($name, $this->paths);

        return parent::findTemplate($name);
    }
}