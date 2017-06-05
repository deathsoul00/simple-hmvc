<?php
namespace Core;

use Core\Exception\LogicException;
use Core\Exception\InvalidArgumentException;

class Database extends \Zend\Db\Adapter\Adapter
{
    /**
     * transform placeholders to its proper value
     * 
     * @param  string $query      a raw query string to be replaced
     * @param  array  $parameters list of parameters
     * 
     * @return string             a replaced placeholders in the query
     */
    public function processQuery($query, array $parameters = [])
    {
        $connection_parameters = $this->getDriver()->getConnection()->getConnectionParameters();
        $table_prefix = isset($connection_parameters['table_prefix']) ? $connection_parameters['table_prefix'] : '';

        // replace '?:' with its table prefix
        $query = str_replace('?:', $table_prefix, $query);

        $parameter_placeholders = [
            0 => '?i', // integer
            1 => '?s', // string
            2 => '?n', // array of integer
            3 => '?a', // array of values indexed by [n]
            4 => '?l', // string of LIKE comparison '%[value]%'
            5 => '?v', // array of values for INSERT query ['field' => 'value']
            6 => '?u', // array of values for UPDATE query ['field' => 'value']
            7 => '?p', // a string for unescap
        ];

        // escape placeholders with proper regex escape
        $parameter_placeholders = array_map(function($placeholder) {
            return preg_quote($placeholder);
        }, $parameter_placeholders);

        $patterns = implode('|', $parameter_placeholders);
        $cursor = 0;

        if (preg_match_all("/$patterns/", $query, $matches)) {
            if (count($matches[0]) !== count($parameters)) {
                throw new LogicException(sprintf('Number of placeholder does not match the number of parameters given, %d given', count($parameters)));
            } elseif (!count($matches[0])) {
                return $query;
            }
        }

        $query = preg_replace_callback("/$patterns+/", function($match) use ($parameters, &$cursor) {
            $replace = '';
            if ($match[0] == '?i') {
                $replace = intval($parameters[$cursor]);
            } elseif ($match[0] == '?s') {
                $replace = $this->getPlatform()->quoteValue($parameters[$cursor]);
            } elseif ($match[0] == '?n') {
                if (!is_array($parameters[$cursor])) {
                    throw new InvalidArgumentException(sprintf('Placeholder ?n only accepts array as datatype, %s given', gettype($parameters[$cursor])));
                }
                $parameter = array_map('intval', $parameters[$cursor]);
                $replace = implode(',', $parameter);
            } elseif ($match[0] == '?a') {
                if (!is_array($parameters[$cursor])) {
                    throw new InvalidArgumentException(sprintf('Placeholder ?s only accepts array as datatype, %s given', gettype($parameters[$cursor])));
                }
                $parameter = array_map(function($parameter) {
                    return $this->getPlatform()->quoteValue($parameter);
                }, $parameters[$cursor]);
                $replace = implode(',', $parameter);
            } elseif ($match[0] == '?l') {
                $replace = $this->getPlatform()->quoteValue($parameters[$cursor]);
            } elseif ($match[0] == '?v') {
                if (!is_array($parameters[$cursor])) {
                    throw new InvalidArgumentException(sprintf('Placeholder ?v only accepts array as datatype, %s given', gettype($parameters[$cursor])));
                }
                $fields = [];
                $values = [];
                foreach ($parameters[$cursor] as $field => $value) {
                    $fields[] = $this->getPlatform()->quoteIdentifier($field);
                    $values[] = $this->getPlatform()->quoteValue($value);
                }
                $replace = sprintf('(%s) VALUES (%s)', implode(',', $fields), implode(',', $values));
            } elseif ($match[0] == '?u') {
                if (!is_array($parameters[$cursor])) {
                    throw new InvalidArgumentException(sprintf('Placeholder ?u only accepts array as datatype, %s given', gettype($parameters[$cursor])));
                }
                $replace = 'SET ';
                foreach ($parameters[$cursor] as $field => $value) {
                    $replace .= sprintf('%s=%s',  $this->getPlatform()->quoteIdentifier($field), $this->getPlatform()->quoteValue($value));
                }
            } elseif ($match[0] == '?p') {
                if (!is_string($parameters[$cursor])) {
                    throw new InvalidArgumentException(sprintf('Placeholder ?p only accepts string as datatype, %s given', gettype($parameters[$cursor])));
                }
                $replace = $parameters[$cursor];
            }

            ++$cursor;
            return $replace;
        }, $query);

        return $query;
    }

    /**
     * get single result from the query
     * 
     * @param  string $query      a raw query string to be replaced
     * @param  array  $parameters list of parameters
     * 
     * @return array|\ArrayObject
     */
    public function getRow($query, array $parameters = [])
    {
        return $this->q($query, $parameters)->current();
    }

    /**
     * get row results from the query
     * 
     * @param  string $query      a raw query string to be replaced
     * @param  array  $parameters list of parameters
     * 
     * @return array|\ArrayObject
     */
    public function getRows($query, array $parameters = [])
    {
        $results = $this->q($query, $parameters);
        $store_results = null;

        if ($results->count()) {
            foreach ($results as $result) {
                $store_results[] = $result;
            }
        }

        return $store_results;
    }

    /**
     * executes the query given
     * 
     * @param  string $query      a raw query string to be replaced
     * @param  array  $parameters list of parameters
     * 
     * @return integer|boolean|Zend\Db\Adapter\Driver\ResultInterface
     */
    public function q($query, array $parameters = [])
    {
        $result = parent::query($this->processQuery($query, $parameters), parent::QUERY_MODE_EXECUTE);

        if (stripos($query, 'INSERT INTO') !== false) {
            return (int)$result->getGeneratedValue();
        } elseif (stripos($query, 'UPDATE') !== false || stripos($query, 'DELETE') !== false) {
            return (bool)$result->getAffectedRows();
        } else {
            return $result;
        }
    }
}
