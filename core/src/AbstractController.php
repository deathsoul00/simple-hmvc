<?php
namespace Core;

use Symfony\Component\DependencyInjection\Container;

/**
 * AbstractController for controllers in this framework
 *
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */
abstract class AbstractController
{
    /**
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * class constructor
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * get container
     * 
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * get layout defined in the class
     * 
     * @return string
     */
    public function getLayout()
    {
        return $this->getContainer()->get('template')->getAssignedVar('_layout');
    }

    /**
     * get template defined in the class
     * 
     * @return string
     */
    public function getTemplate()
    {
        return $this->getContainer()->get('template')->getAssignedVar('_template');
    }

    /**
     * set the class layout
     * 
     * @param string $layout filename of the layout
     */
    public function setLayout($layout)
    {
        $this->getContainer()->get('template')->assignVar('_layout', $layout);
    }

    /**
     * set the class template
     * 
     * @param string $template filename of the template
     */
    public function setTemplate($template)
    {
        $this->getContainer()->get('template')->assignVar('_template', $template);
    }
}
