<?php
namespace Core;

use Core\Helper\ArrayHelper;

/**
 * Custom Template Class that extends the functionality of \Mustache_Engine Class
 *
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */
class Template extends \Mustache_Engine
{
    /**
     * storage for template variables
     *
     * @var array
     */
    protected $variables = [];

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
        if (!is_string($key)) {
            throw new \InvalidArgumentException(sprintf('key must be a string, %s given', getType($key)));
        }

        $this->variables[$key] = $var;
    }

    /**
     * get assigned variable from template
     * 
     * @param  string $key can be a dot-notation string to access array variables
     * 
     * @return mixed
     */
    public function getAssignedVar($key)
    {
        if (!is_string($key)) {
            throw new \InvalidArgumentException(sprintf('key must be a string, %s given', getType($key)));
        }

        return ArrayHelper::dot($this->getAssignedVars(), $key);
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
}
