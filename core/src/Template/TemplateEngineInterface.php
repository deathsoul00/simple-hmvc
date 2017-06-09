<?php
namespace Core\Template;

interface TemplateEngineInterface
{
    /**
     * initialize the class
     * 
     * @return TemplateEngineInterface
     */
    public function initialize(array $config);

    /**
     * function to assign variables to template
     *
     * @param  string $key key name that will be used in template
     * @param  mixed  $var variable to be assigned to template
     *
     * @return void
     */
    public function assignVar($key, $var);

    /**
     * get assigned variable from template
     *
     * @param  string $key can be a dot-notation string to access array variables
     *
     * @return mixed
     */
    public function getAssignedVar($key, $default = null);

    /**
     * get all the assigned vars in the template
     *
     * @return array
     */
    public function getAssignedVars();

    /**
     * Outputs the template and its content
     * 
     * @return void
     */
    public function output();
}