<?php
namespace Core\Template;

use Core\Helper\ArrayHelper;
use Core\Exception\InvalidArgumentException;
use Core\Loader\Twig\TemplateFilesystemLoader;

class TwigEngine implements TemplateEngineInterface
{
    /**
     * Twig Engine
     *
     * @var \Twig_Environment
     */
    protected $engine = null;

    /**
     * array of variables to be pass to template
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
            $fs_loader = new TemplateFilesystemLoader($config['template_dir']);
            $this->engine = new \Twig_Environment($fs_loader, $config);
            
            if (array_key_exists('token_parsers', $config)) {
                $this->registerTokenParsers($config['token_parsers']);
            }
        }

        return $this;
    }

    /**
     * TemplateEngineInterface::assignVar
     *
     * @param  string $key key of the variable
     * @param  mixed  $var value of the variable to be assigned
     *
     * @return void
     */
    public function assignVar($key, $var)
    {
        $this->isValidKey($key);

        $this->variables[$key] = $var;
    }

    /**
     * TemplateEngineInterface::getAssignedVar
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
     * TemplateEngineInterface::getAssignedVars
     * 
     * @return array array of variables assigned to the templates
     */
    public function getAssignedVars()
    {
        return $this->variables;
    }

    /**
     * TemplateEngineInterface::output
     * 
     * @return void
     */
    public function output()
    {
        echo $this->engine->render($this->getAssignedVar('_template'), $this->getAssignedVars());
    }

    public function registerTokenParsers(array $parsers)
    {
        if ($parsers && is_array($parsers)) {
            foreach ($parsers as $name => $class) {
                if (class_exists($class)) {
                    $this->engine->addTokenParser(new $class);
                }
            }
        }
    }

}