<?php
namespace Core\Exception;

/**
 * AbstractException parent of all the Core Exception Classes in the Core\Exception namespace
 *
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */
abstract class AbstractException extends \Exception implements ExceptionInterface
{
    /**
     * inherits Core\Exception\ExceptionInterface::display
     * 
     * @return mixed
     */
    final public function display() {
        echo $this->getMessage();
    }
}
