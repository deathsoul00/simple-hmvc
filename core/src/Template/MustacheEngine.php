<?php
namespace Core\Template;

use Core\Registry;
use Core\Helper\ArrayHelper;
use Core\Loader\MustacheTemplateLoader;
/**
 * Custom Template Class that extends the functionality of \Mustache_Engine Class
 *
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */
class MustacheEngine implements TemplateEngineInterface
{
    /**
     * mustache class
     *
     * @var \Mustache_Engine
     */
    protected $engine = null;

    /**
     * storage for template variables
     *
     * @var array
     */
    protected $variables = [];

    /**
     * override calling of methods to Twig_Engine class
     *
     * @param  string $name      name of the method
     * @param  array  $arguments array of arguments
     *
     * @return mixed
     */
    public function __call($name, array $arguments = [])
    {
        if (is_string($name) && is_callable(array($this->engine, $name))) {
            return call_user_func_array([$this->engine, $name], $arguments);
        }
    }

    /**
     * check if key is a valid string
     * 
     * @param  string  $key key of the variable
     * 
     * @return boolean
     * 
     * @throws Core\Exception\InvalidArgumentException
     */
    public function isValidKey($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException(sprintf('key must be a valid string, %s given', gettype($key)));
        }

        return true;
    }

    /**
     * initialize engine
     *
     * @param  array  $config config of template
     *
     * @return TemplateEngineInterface
     */
    public function initialize(array $config)
    {
        if ($this->engine == null) {
            $options = !empty($config['options']) ? $config['options'] : [];

            // attach main template loader
            $config['loader'] = new MustacheTemplateLoader("$config[templates_path]/", $options);
            // attach partial template loader
            $config['partials_loader'] = new MustacheTemplateLoader("$config[partial_templates_path]/", $options);

            // removed unnecessary configuration
            unset($config['templates_path']);
            unset($config['partial_templates_path']);
            unset($config['options']);

            $this->engine = new \Mustache_Engine($config);
        }

        return $this;
    }

    /**
     * function to assign variables to template
     *
     * @param  string $key key name that will be used in template
     * @param  mixed  $var variable to be assigned to template
     *
     * @return void
     */
    public function assignVar($key, $var)
    {
        $this->isValidKey($key);

        $this->variables[$key] = $var;
    }


    /**
     * get assigned variable from template
     *
     * @param  string $key      key of the variable assigned can be a dot-notation string
     * @param  mixed  $default  this will be return if value of the key is not existed
     *
     * @return mixed
     */
    public function getAssignedVar($key, $default = null)
    {
        $this->isValidKey($key);

        return ArrayHelper::dot($this->getAssignedVars(), $key) ?: $default;
    }

    /**
     * get all the assigned vars in the template
     *
     * @return array
     */
    public function getAssignedVars()
    {
        return $this->variables;
    }

    /**
     * Outputs the template and its content
     *
     * @return void
     */
    public function output()
    {
        $vars = $this->getAssignedVars();
        $content = $this->render($vars['_template'], $vars);

        if (!empty($vars['_layout'])) {
            $vars['content'] = $content;
            echo $this->render($vars['_layout'], $vars);
        } else {
            echo $content;
        }
    }

    /**
     * renders the template
     * 
     * @param  string $template_name name of the file to be rendered
     * @param  array  $variables     array of variables to be assigned
     * 
     * @return string                content of the file
     */
    public function render($name, array $variables = [])
    {
        return $this->engine->render($name, $variables);
    }

    /**
     * returns the fs loader of template
     * 
     * @return \Core\Loader\TemplateLoaderInterface
     */
    public function getLoader()
    {
        return $this->engine->getLoader();
    }
}
