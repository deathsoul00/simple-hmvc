<?php
namespace Core\Factory;

use Core\Config;
use Core\Database;
use Zend\Db\ResultSet\ResultSet;

class DatabaseFactory implements FactoryInterface
{
    public static function create()
    {
        $configuration = Config::get('database');
        $options = $configuration['options'];

        // define what should be our database return results as
        if ($options['return_type'] == 'array') {
            $resultset = new ResultSet(ResultSet::TYPE_ARRAY);
        } elseif ($options['return_type'] == 'array_object') {
            $resultset = new ResultSet(ResultSet::TYPE_ARRAYOBJECT);
        } else {
            $resultset = null;
        }

        return new Database($configuration, null, $resultset);
    }
}
