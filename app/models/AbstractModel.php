<?php

namespace App\Models;


use Phalcon\Db\Adapter\Pdo\Postgresql;

abstract class AbstractModel extends \Phalcon\Mvc\Model
{
    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public abstract function validation();

    public function create($data = null, $whiteList = null)
    {
        $fields = join(',', array_keys($data));
        $values = join('\', \'', array_values($data));

        $sql = "insert into {$this->getSource()}($fields) VALUES ('$values')";

        /** @var Postgresql $db */
        $db = $this->_dependencyInjector->get('db');
        $db->execute($sql);

        return true;
    }
}