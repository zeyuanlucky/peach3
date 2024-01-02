<?php
namespace Peach2\Framework\Mvc\Model;
class Model{

    protected $_instance;

    public function __construct(){
        require_once (ROOT_PATH.'vendor'.DIRECTORY_SEPARATOR.'thingengineer'.DIRECTORY_SEPARATOR.'mysqli-database-class'. DIRECTORY_SEPARATOR .'MysqliDb.php');
        $config = MYSQLI_SINGLE;
        $this->_instance = new \MysqliDb ($config['host'], $config['user'], $config['password'], $config['dbname'], $config['port']);
        $this->_instance->setPrefix ($config['table_prefix']);
    }
}