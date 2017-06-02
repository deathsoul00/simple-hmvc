<?php
namespace Core\Exception;

/**
 * ExceptionInterface is the interface implemented by all the Custom Exception class in Core classes
 *
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */
interface ExceptionInterface
{
    /**
     * this will display the exception thrown in a specific way
     * 
     * @return mixed
     */
    public function display();
}
