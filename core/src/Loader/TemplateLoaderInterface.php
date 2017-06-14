<?php
namespace Core\Loader;

/**
 * interface for template filesystem loader
 *
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */
interface TemplateLoaderInterface
{
    /**
     * returns the base paths of the template
     * 
     * @return array
     */
    public function getPaths();
}